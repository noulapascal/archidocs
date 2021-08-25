<?php

namespace App\Form;

use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('sigle')
            ->add('code')
            ->add('logo', FileType::class, [
                'multiple' => false
            ])
            ->add('banner', FileType::class, [
                'multiple' => false
            ])
            ->add('description')
            ->add('mailAddress')
            ->add('phoneNumber')
            ->add('city')
            ->add('locality');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
        ]);
    }
}
