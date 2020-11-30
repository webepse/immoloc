<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
//use Cocur\Slugify\Slugify;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Booking;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    // gestion du hash de password
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

   
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR-fr');
        //$slugify = new Slugify();

        $admin = new User();
        $admin->setFirstName('Jordan')
            ->setLastName('Berti')
            ->setEmail('berti@epse.be')
            ->setPassword($this->encoder->encodePassword($admin,'password'))
            ->setIntroduction($faker->sentence())
            ->setDescription('<p>'.join('</p><p>', $faker->paragraphs(3)).'</p>')
            ->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);



        // gestion des utilisateurs 
        $users = []; // initialisation d'un tableau pour associer Ad et User
        $genres = ['male','femelle'];

        for($u=1; $u <= 10; $u++){
            $user = new User();
            $genre = $faker->randomElement($genres);

            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1,99).'.jpg';
            // https://randomuser.me/api/portraits/women/25.jpg 
            // $picture = $picture.'women/.'25.jpg'
            $picture .= ($genre == 'male' ? 'men/' : 'women/').$pictureId;

            $hash = $this->encoder->encodePassword($user,'password');

            $user->setFirstName($faker->firstName($genre))
                ->setLastName($faker->lastName())
                ->setEmail($faker->email())
                ->setIntroduction($faker->sentence())
                ->setDescription('<p>'.join('</p><p>', $faker->paragraphs(3)).'</p>')
                ->setPassword($hash)
                ->setPicture('');

            $manager->persist($user);
            $users[]= $user; // ajouter l'utilisateur fraichement créé dans le tableau pour l'association avec les annonces    

        }

        // gestion des annonces
        for($a = 1; $a <= 30; $a++){
            $ad = new Ad();
            $title = $faker->sentence();
            //$slug = $slugify->slugify($title);
            //$coverImage = $faker->imageUrl(1000,350);
            $introduction = $faker->paragraph(2);
            $content = '<p>'.join('</p><p>',$faker->paragraphs(5)).'</p>';
            $user = $users[rand(0,count($users)-1)];

            $ad->setTitle($title)
                ->setCoverImage('https://picsum.photos/1000/350')
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(rand(40,200))
                ->setRooms(rand(1,5))
                ->setAuthor($user)
                ;

            $manager->persist($ad); 
            
            for($i=1; $i <= rand(2,5); $i++){
                $image = new Image();
                $image->setUrl('https://picsum.photos/200/200')
                    ->setCaption($faker->sentence())
                    ->setAd($ad);
                $manager->persist($image);    
            }

            // gestion des réservation 
            for($b = 1; $b <= rand(0,10); $b++){
                $booking = new Booking();
                $createdAt = $faker->dateTimeBetween('-6 months','-4 months');
                $startDate = $faker->dateTimeBetween('-3 months');

                $duration = rand(3,10);
                // objet datetime de php
                // $startDate->modify("+5 days") parce que modify va modifier la startDate directement, on doit cloner l'objet
                $endDate = (clone $startDate)->modify("+$duration days");
                $amount = $ad->getPrice() * $duration;
                $comment = $faker->paragraph();
                $booker = $users[rand(0,count($users)-1)];

                $booking->setBooker($booker)
                        ->setAd($ad)
                        ->setStartDate($startDate)
                        ->setEndDate($endDate)
                        ->setCreatedAt($createdAt)
                        ->setAmount($amount)
                        ->setComment($comment)
                        ;
                $manager->persist($booking);

            }
            


        }

        $manager->flush();
    }
}
