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
class ProfileType extends AbstractType
{

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('username')
            ->add('name')
            ->add('email')
            ->add('dateOfBirth')
            ->add('phoneNumber')
            ->add('address')
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
