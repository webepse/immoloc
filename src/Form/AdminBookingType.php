<?php

namespace App\Form;

use App\Entity\Ad;
use App\Entity\User;
use App\Entity\Booking;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AdminBookingType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', DateType::class, $this->getConfiguration("Date d'arrivée", "La date à laquelle vous comptez arriver"))
            ->add('endDate', DateType::class, $this->getConfiguration('Date de départ', "La date à laquelle vous comptez partir"))
            ->add('comment', TextareaType::class, $this->getConfiguration("Commentaire", "Si vous avez un commentaire, n'hésitez pas à en faire part",[
                "required"=>false
            ]))
            /*
            ->add('booker', EntityType::class, $this->getConfiguration("Locataire", false,[
                'class' => User::class,
                'choice_label' => 'fullName'
            ]))
            */
            ->add('booker', EntityType::class, $this->getConfiguration("Locataire", false,[
                'class' => User::class,
                'choice_label' => function($user){
                    return $user->getFirstName(). " " . strtoupper($user->getLastName());
                }
            ]))
            ->add('ad', EntityType::class, $this->getConfiguration("Annonce", false, [
                'class' => Ad::class,
                'choice_label' => 'title'
            ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
