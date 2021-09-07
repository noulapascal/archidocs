<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity\Company;
use App\Repository\CompanyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/login")
 */


class SecurityController extends AbstractController
{

    /**
     * @Route("/", name="app_login")
     */

    public function login(AuthenticationUtils $authenticationUtils,Request $request): Response
    {

        if ($this->getUser()) {
            $userLocale = $this->getUser()->getLocale();

            return $this->redirectToRoute('directory_index',['_locale'=> !empty($userLocale)?$userLocale : $request->getLocale()]);
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }




    /**
     * @Route("/company", name="login")
     */
    public function CompanyLogin(AuthenticationUtils $authenticationUtils, CompanyRepository $companyRepo, Request $request): Response
    {

        $session = new Session();
        $session->start();
        if ($this->getUser()) {
            return $this->redirectToRoute('directory_index');
        }

        if (!empty($code = $request->get('company'))) {
            $code = $request->get('company');
            
            $company = $companyRepo->findOneBy([
                'code' => $code
            ]);

            $session->set('name', $company->getCode());

            return    $this->redirectToRoute('app_login');
        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        // $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'security/company.login.html.twig',
            [
                'companies' =>
                $companyRepo->findAll(),
                'error' => $error
            ]
        );
    }


    /**
     * @Route("/login/{id}", name="company_login")
     */
    public function loginWithCompany(AuthenticationUtils $authenticationUtils, Company $company): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('directory_index');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'company' => $company,
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
