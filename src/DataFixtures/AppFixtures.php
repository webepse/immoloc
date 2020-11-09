<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
//use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR-fr');
        //$slugify = new Slugify();

        for($a = 1; $a <= 30; $a++){
            $ad = new Ad();
            $title = $faker->sentence();
            //$slug = $slugify->slugify($title);
            $coverImage = $faker->imageUrl(1000,350);
            $introduction = $faker->paragraph(2);
            $content = '<p>'.join('</p><p>',$faker->paragraphs(5)).'</p>';

            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(rand(40,200))
                ->setRooms(rand(1,5));

            $manager->persist($ad);    

        }
        $manager->flush();
    }
}
