<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('name', null, [
            'label' => 'Nom complet',
        ])
        ->add('adresse', null, [
            'label' => 'Adresse complète',
        ])
        ->add('zip', null, [
            'label' => 'Code postal',
        ])
        ->add('city', null, [
            'label' => 'Ville',
        ])
        ->add('mail', null, [
            'label' => 'Adresse email',
        ])
        ->add('phone', null, [
            'label' => 'Numéro de téléphone',
        ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
