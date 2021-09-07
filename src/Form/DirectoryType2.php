<?php

namespace App\Form;

use App\Entity\Directory;
use App\Entity\CompanyDivision;
use Symfony\Component\Form\AbstractType;
use App\Repository\CompanyDivisionRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DirectoryType2 extends AbstractType
{

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('companyDivision',EntityType::class,[
                'class' => CompanyDivision::class,
                'multiple'=> true,
                'query_builder' => function (CompanyDivisionRepository $repo){
                    $company = $this->security->getUser()->getDivision()->getCompany()->getId();
                    return $repo->findByCompany($company);
                }

            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Directory::class,
        ]);
    }
}
