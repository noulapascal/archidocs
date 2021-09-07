<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;


class BookType extends AbstractType
{

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('author')
            ->add('addedAt')
            ->add('updateAt')
            ->add('reference')
            ->add('available')
            ->add('takenBy',EntityType::class,[
                'class' => User::class,
                'required'=> false,
                'query_builder' => function (UserRepository $repo){
                    $company = $this->security->getUser()->getDivision()->getCompany()->getId();
                    return $repo->findByCompany($company);
                }

            ])        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
