<?php

namespace App\Form;

use App\Entity\Annuary;
use App\Entity\Contact;
use App\Repository\AnnuaryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ContactType extends AbstractType
{
    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('phoneNumberOne')
            ->add('phoneNumberTwo')
            ->add('phoneNumberThree')
            ->add('Email')
            ->add('address')
            ->add('city')
            ->add('annuaire',EntityType::class,[
                'class' => Annuary::class,
                'multiple'=> true,
                'query_builder' => function (AnnuaryRepository $repo){
                    $company = $this->security->getUser()->getDivision()->getCompany()->getId();
                    return $repo->findByCompany($company);
                }

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
