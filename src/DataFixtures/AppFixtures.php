<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Cars;
use App\Entity\Image;
use Cocur\Slugify\Slugify;
use Faker\Provider\Fakecar;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Fakecar($faker)); // Appel au faker de voiture
      
        // Tableau d'images pour les distribuer aléatoirement
        $carImages = [];

        for ($i = 1; $i <= 25; $i++) { //boucle FOR pour changer le numéro en jpg
            
            $newURL = 'https://hips.hearstapps.com/hmg-prod/amv-prod-cad-assets/wp-content/uploads/2017/04/cars_3_characters-' . $i . '.jpg';
            $carImages[] = $newURL; //on ajoute la nouvelle URL dans le tableau
        }

        for ($i = 1; $i <= 25; $i++) {
            $randomImage = $carImages[array_rand($carImages)]; // Récupère aléatoirement une image du tableau
            $cars = new Cars();
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
                ->setOptions('<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p');
                // Gestion de la galerie image de la voiture
                for($g=1; $g <= rand(2,5); $g++)
                {
                    $image = new Image();
                    $image->setUrl('https://picsum.photos/id/'.$g.'/900')
                        ->setCaption($faker->sentence())
                        ->setCar($cars);
                    $manager->persist($image);    
                }


            $manager->persist($cars);
        }

        $manager->flush();
    }
}
