<?php

namespace App\Form;

use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\DataTransformer\StringToFileTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class CompanyType extends AbstractType
{

        
    public function __construct(StringToFileTransformer $transformer)
    {
        $this->transformer = $transformer;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('sigle')
            ->add('code')
            ->add('logo', FileType::class, [
                'multiple' => false,
                'required' =>false,
                'mapped' => false
            ])
            ->add('banner', FileType::class, [
                'multiple' => false,
                'required' => false,
                'mapped' => false
            ])
            ->add('description')
            ->add('mailAddress')
            ->add('phoneNumber')
            ->add('city')
            ->add('locality');


          /*  $builder->get('logo')
            ->addModelTransformer($this->transformer);
         $builder->get('banner')
            ->addModelTransformer($this->transformer);
       */
            

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
        ]);
    }
}
