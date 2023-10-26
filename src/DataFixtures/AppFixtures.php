<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Cars;
use App\Entity\User;
use App\Entity\Image;
use DateTimeImmutable;
use App\Entity\Comment;
use Cocur\Slugify\Slugify;
use Faker\Provider\Fakecar;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Fakecar($faker)); // Appel au faker de voiture
         // gestion des utilisateurs 
         $users = []; // init d'un tableau pour récup des user pour les voitures
         $genres = ['male','femelle'];
 
         for($u=1 ; $u <= 10; $u++)
         {
             $user = new User();
             $genre = $faker->randomElement($genres);
           
 
             $hash = $this->passwordHasher->hashPassword($user, 'password');
           
             $user->setFirstName($faker->firstName($genre))
                 ->setLastName($faker->lastName())
                 ->setEmail($faker->email())
                 ->setIntroduction($faker->sentence())
                 ->setDescription('<p>'.join('</p><p>',$faker->paragraphs(3)).'</p>')
                 ->setPassword($hash)
                 ->setPicture('');
                
              
 
             $manager->persist($user); 
             $users[] = $user;   
 
            // ajouter un user au tableau (pour les voitures)
 
         }
      
        // Tableau d'images pour les distribuer aléatoirement
        $carImages = [];

        for ($i = 1; $i <= 25; $i++) { //boucle FOR pour changer le numéro en jpg
            
            $newURL = 'https://hips.hearstapps.com/hmg-prod/amv-prod-cad-assets/wp-content/uploads/2017/04/cars_3_characters-' . $i . '.jpg';
            $carImages[] = $newURL; //on ajoute la nouvelle URL dans le tableau
        }

        for ($i = 1; $i <= 25; $i++) {
            $randomImage = $carImages[array_rand($carImages)]; // Récupère aléatoirement une image du tableau
            $cars = new Cars();
            $user = $users[rand(0, count($users)-1)];
            $cars->setModel($faker->vehicleModel)
                ->setBrand($faker->vehicleBrand)
                ->setCoverImage($randomImage)
                ->setKm(rand(5000, 150000))
                ->setPrice(rand(10000, 300000))
                ->setOwners(rand(1, 4))
                ->setCylinder(rand(1600, 3500))
                ->setPower(rand(200, 800))
                ->setFuel($faker->vehicleFuelType)
                ->setYear(rand(2000, 2023))
                ->setTransmission($faker->vehicleGearBoxType)
                ->setContent('<p>' . join('</p><p>', $faker->paragraphs(2)) . '</p>')
                ->setOptions('<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p')
                ->setAuthor($user);
                // Gestion de la galerie image de la voiture
                for($g=1; $g <= rand(2,5); $g++)
                {
                    $image = new Image();
                    $image->setUrl('https://picsum.photos/id/'.$g.'/900')
                        ->setCaption($faker->sentence())
                        ->setCar($cars);
                    $manager->persist($image);    
                }
                for ($b = 1; $b <= rand(0, 10); $b++) {
                    $createAt = $faker->dateTimeBetween('-6 months', "-4 months");
                    $createdAt = new DateTimeImmutable($createAt->format('Y-m-d'));
                    // Gestion des commentaires
                    $comment = new Comment();
                    $comment->setContent($faker->paragraph())
                        ->setRating(rand(1, 5))
                        ->setAuthor($user)
                        ->setCar($cars)
                        ->setCreateAt($createdAt);
                    $manager->persist($comment);
                }


            $manager->persist($cars);
        }

        $manager->flush();
    }
}
