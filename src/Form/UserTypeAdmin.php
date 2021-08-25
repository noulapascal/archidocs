<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\CompanyDivision;
use App\Repository\CompanyRepository;
use App\Repository\DivisionRepository;
use Symfony\Component\Form\AbstractType;
use App\Repository\CompanyDivisionRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Security\Core\Security;
class UserTypeAdmin extends AbstractType
{

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('username')
            ->add('password',RepeatedType::class,[

                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
                'type' => PasswordType::class,
            
                ])
                ->add('roles', ChoiceType::class,[
                    'choices'=>[
                        'HABITECH'=>"ROLE_HABITECH",
                        'ADMINISTRATEUR'=>"ROLE_ADMIN",
                        'USER'=>"ROLE_USER",           
                        
                        ],
                        'label'=>'Account type',
                    'mapped'=>false,
                    'required'=>true
                ])
            ->add('name')
            ->add('email')
            ->add('division',EntityType::class,[
                'class' => CompanyDivision::class,
                'query_builder' => function (CompanyDivisionRepository $repo){
                    return $repo->findByCompany($this->security->getUser()->getDivision()->getCompany()->getId);
                                }
                ])   
     
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
