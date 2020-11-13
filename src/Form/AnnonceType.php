<?php

namespace App\Form;

use App\Entity\Ad;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AnnonceType extends AbstractType
{
    private function getConfiguration($label,$placeholder){
        return [
            'label'=>$label,
            'attr'=> [
                'placeholder'=>$placeholder
            ]
        ];
    }



    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => "Titre",
                'attr' => [
                    'placeholder'=>'titre de votre annonce'
                ]
            ])
            ->add('slug', TextType::class, $this->getConfiguration('Slug','Adresse web (automatique)'))
            ->add('rooms', IntegerType::class, $this->getConfiguration('Nombre de chambres','Donnez le nombre de chambres disponibles'))
            ->add('price', MoneyType::class, $this->getConfiguration('Prix par nuit','indiquer le prix que vous voulez pour une nuit'))
            ->add('introduction', TextType::class, $this->getConfiguration('Introduction','Donnez une description globale de l\'annonce'))
            ->add('content', TextareaType::class, $this->getConfiguration('Description','Déscription détaillée de votre bien'))
            ->add('coverImage', UrlType::class, $this->getConfiguration('URL de l\'image','Donnez l\'adresse de votre image'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
