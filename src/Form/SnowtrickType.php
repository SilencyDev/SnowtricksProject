<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Snowtrick;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Form\Type\CustomUrlType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SnowtrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ])
           ->add('videos', CollectionType::class, [
               'entry_type' => CustomUrlType::class,
               'label' => false,
               'allow_add' => true,
               'prototype' => true,
               'delete_empty' => true,
               'required' => false,
               'mapped' => false,
            ])
            ->add('validated', CheckboxType::class, [
                'required' => false,
                'data' => false,
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $trick = $event->getData();
            $form = $event->getForm();

            if ($trick || null === $trick->getMainpicture()) {
                $form->add('mainfile', FileType::class, [
                    'label' => 'Upload a main picture',
                    'required' => false,
                ]);
            }

            if ($trick || null === $trick->getPictures()) {
                $form->add('file', FileType::class, [
                    'label' => 'Upload a picture',
                    'multiple' => true,
                    'required' => false,
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Snowtrick::class,
        ]);
    }
}
