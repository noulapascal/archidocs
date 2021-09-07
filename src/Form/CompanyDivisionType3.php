<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\CompanyDivision;
use App\Repository\CompanyRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyDivisionType3 extends AbstractType
{
    public function __construct(Security $security, CompanyRepository $compRepo)
    {
        $this->security = $security;
        $this->compRepo = $compRepo;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('company',EntityType::class,[
            'class'=> Company::class,
            'choices' => [$this->security->getUser()?$this->security->getUser()->getDivision()->getCompany():$this->compRepo->findAll()],
        ])
        ->add('name')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CompanyDivision::class,
        ]);
    }
}
