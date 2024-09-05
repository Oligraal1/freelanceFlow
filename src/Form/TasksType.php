<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\Tasks;
use App\Form\ProjectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;

class TasksType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label'=>'Nom de la tâche'
            ])
            ->add('taskDate', DateType::class, [
                'label'=>'Dâte d\'exécution de la tâche',
                'widget' => 'single_text',
                'html5' => false,
            ])
            ->add('hourWorked', null, [
                'label'=>'Nombre d\'heures travaillées sur la tâche'
            ])
            ->add('description', TextareaType::class, [
                'label'=>'Description de la tâche'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tasks::class,
           
        ]);
    }
}
