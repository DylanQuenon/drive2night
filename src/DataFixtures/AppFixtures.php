<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Cars;
use Cocur\Slugify\Slugify;
use Faker\Provider\Fakecar;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Fakecar($faker)); //appel au faker de voiture
      
        //tableau d'image pour les distribuer aléatoirement
        $carImages = [
        'https://hips.hearstapps.com/hmg-prod/amv-prod-cad-assets/wp-content/uploads/2017/04/cars_3_characters-1.jpg',
        'https://hips.hearstapps.com/hmg-prod/amv-prod-cad-assets/wp-content/uploads/2017/04/cars_3_characters-2.jpg',
        'https://hips.hearstapps.com/hmg-prod/amv-prod-cad-assets/wp-content/uploads/2017/04/cars_3_characters-3.jpg',
        'https://hips.hearstapps.com/hmg-prod/amv-prod-cad-assets/wp-content/uploads/2017/04/cars_3_characters-4.jpg',
        'https://hips.hearstapps.com/hmg-prod/amv-prod-cad-assets/wp-content/uploads/2017/04/cars_3_characters-5.jpg',
        'https://hips.hearstapps.com/hmg-prod/amv-prod-cad-assets/wp-content/uploads/2017/04/cars_3_characters-6.jpg',
        'https://hips.hearstapps.com/hmg-prod/amv-prod-cad-assets/wp-content/uploads/2017/04/cars_3_characters-7.jpg',
        'https://hips.hearstapps.com/hmg-prod/amv-prod-cad-assets/wp-content/uploads/2017/04/cars_3_characters-8.jpg',
        'https://hips.hearstapps.com/hmg-prod/amv-prod-cad-assets/wp-content/uploads/2017/04/cars_3_characters-9.jpg',
        'https://hips.hearstapps.com/hmg-prod/amv-prod-cad-assets/wp-content/uploads/2017/04/cars_3_characters-10.jpg',
        'https://hips.hearstapps.com/hmg-prod/amv-prod-cad-assets/wp-content/uploads/2017/04/cars_3_characters-11.jpg',
        'https://hips.hearstapps.com/hmg-prod/amv-prod-cad-assets/wp-content/uploads/2017/04/cars_3_characters-12.jpg',
        'https://hips.hearstapps.com/hmg-prod/amv-prod-cad-assets/wp-content/uploads/2017/04/cars_3_characters-13.jpg',
        'https://hips.hearstapps.com/hmg-prod/amv-prod-cad-assets/wp-content/uploads/2017/04/cars_3_characters-14.jpg',
        'https://hips.hearstapps.com/hmg-prod/amv-prod-cad-assets/wp-content/uploads/2017/04/cars_3_characters-15.jpg',
        'https://hips.hearstapps.com/hmg-prod/amv-prod-cad-assets/wp-content/uploads/2017/04/cars_3_characters-16.jpg',
        'https://hips.hearstapps.com/hmg-prod/amv-prod-cad-assets/wp-content/uploads/2017/04/cars_3_characters-17.jpg',
        'https://hips.hearstapps.com/hmg-prod/amv-prod-cad-assets/wp-content/uploads/2017/04/cars_3_characters-18.jpg',
        'https://hips.hearstapps.com/hmg-prod/amv-prod-cad-assets/wp-content/uploads/2017/04/cars_3_characters-19.jpg',
        'https://hips.hearstapps.com/hmg-prod/amv-prod-cad-assets/wp-content/uploads/2017/04/cars_3_characters-20.jpg',
        'https://hips.hearstapps.com/hmg-prod/amv-prod-cad-assets/wp-content/uploads/2017/04/cars_3_characters-21.jpg',
        'https://hips.hearstapps.com/hmg-prod/amv-prod-cad-assets/wp-content/uploads/2017/04/cars_3_characters-22.jpg',
        'https://hips.hearstapps.com/hmg-prod/amv-prod-cad-assets/wp-content/uploads/2017/04/cars_3_characters-23.jpg',
        'https://hips.hearstapps.com/hmg-prod/amv-prod-cad-assets/wp-content/uploads/2017/04/cars_3_characters-24.jpg',
        'https://hips.hearstapps.com/hmg-prod/amv-prod-cad-assets/wp-content/uploads/2017/04/cars_3_characters-25.jpg',]; 

        for($i=1;$i<=25;$i++)
        {
            $randomImage = $carImages[array_rand($carImages)];//récupère aléatoirement une image du tableau
            $cars= new Cars();
            $cars->setModel($faker->vehicleModel)
                ->setBrand($faker->vehicleBrand)
                ->setCoverImage($randomImage)
                ->setKm(rand(5000,150000))
                ->setPrice(rand(10000,300000))
                ->setOwners(rand(1,4))
                ->setCylinder(rand(1600,3500))
                ->setPower(rand(200,800))
                ->setFuel($faker->vehicleFuelType)
                ->setYear(rand(2000,2023))
                ->setTransmission($faker->vehicleGearBoxType)
                ->setContent('<p>'.join('</p><p>', $faker->paragraphs(2)).'</p>')
                ->setOptions('<p>'.join('</p><p>', $faker->paragraphs(5)).'</p>');
                $manager->persist($cars);
            }



        $manager->flush();
    }
}
