<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\ProfileType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route(
 *     "/{_locale}/user",
 *     requirements={
 *         "_locale": "en|fr|de",
 *     }
 * ) 
 * @IsGranted("IS_AUTHENTICATED_FULLY") 
 */

class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {

        if (!empty($this->getUser())) {
            if($this->isGranted(('ROLE_ADMIN')))
            {

                return $this->render('user/index.html.twig', [
                    'users' =>  $userRepository->findByCompanyForController
                    (
                        $this->getUser()->getDivision()->getCompany()
                    ),
                ]);

            } elseif ($this->isGranted('ROLE_HABITECH') or $this->isGranted('ROLE_SUPER_ADMIN')) {
                # code...
                return $this->render('user/index.html.twig', [
                    'users' =>  $userRepository->findAll()
                ]);
            }
            # code...
        }

        return $this->render('user/index.html.twig', [
            'users' => ""
        ]);
    }



    /**
     * @Route("/admin", name="user_admin_index", methods={"GET"})
     */
    public function indexadmin(UserRepository $userRepository): Response
    {

        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }


    

        /**
     * @Route("/profile/{id}", name="user_profile", methods={"GET","POST","PUT"})
     */
    public function userProfile(Request $request,User $user, UserPasswordEncoderInterface $passwordEncoder): Response
    {

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            

            $entityManager->persist($user);
            $entityManager->flush();


            return $this->redirectToRoute('user_profile', [
                'id'=>$user->getId(),
            ], Response::HTTP_SEE_OTHER);
        }
       

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'form' =>$form->createView()
        ]);
    }




    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $user->setRoles([$form['roles']]);
            $entityManager->persist($user);
            $entityManager->flush();


            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder,Security $security): Response
    {
        $form = $this->createForm(UserType::class, $user,[
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            if(!empty($form->get('roles')->getData()))
            {
                                $user->setRoles([$form->get('roles')->getData()]);
                             
                                
            }
            else{
                $user->setRoles(['ROLE_USER']);

            }



            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    }
}
