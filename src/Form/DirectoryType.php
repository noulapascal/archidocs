<?php

namespace App\Form;

use App\Entity\Directory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DirectoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('size')
            ->add('type')
            ->add('extension')
            ->add('permissions')
            ->add('path')
            ->add('isFIle')
            ->add('fileType')
            ->add('companyDivision')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Directory::class,
        ]);
    }
}
