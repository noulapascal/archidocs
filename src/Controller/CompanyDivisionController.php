<?php

namespace App\Controller;

use App\Entity\CompanyDivision;
use App\Form\CompanyDivisionType;
use App\Form\CompanyDivisionType2;
use App\Repository\CompanyDivisionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route(
 *     "/{_locale}/companyDivision",
 *     requirements={
 *         "_locale": "en|fr|de",
 *     }
 * ) 
 */
class CompanyDivisionController extends AbstractController
{
    /**
     * @Route("/", name="company_division_index", methods={"GET"})
     */
    public function index(CompanyDivisionRepository $companyDivisionRepository): Response
    {


        if (!empty($this->getUser())) {
            if($this->isGranted(('ROLE_ADMIN')))
            {

                return $this->render('company_division/index.html.twig', [
                    'company_divisions' =>  $companyDivisionRepository->findByCompanyForController
                    (
                        $this->getUser()->getDivision()->getCompany()
                    ),
                ]);

            } elseif ($this->isGranted('ROLE_HABITECH') or $this->isGranted('ROLE_SUPER_ADMIN')) {
                # code...
                return $this->render('company_division/index.html.twig', [
                    'company_divisions' =>  $companyDivisionRepository->findAll()
                ]);
            }
            # code...
        }

        return $this->render('company_division/index.html.twig', [
            'company_divisions' => [],
        ]);
    }

    /**
     * @Route("/new", name="company_division_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $companyDivision = new CompanyDivision();
        $form = $this->createForm(CompanyDivisionType2::class, $companyDivision);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($companyDivision);
            $entityManager->flush();

            return $this->redirectToRoute('company_division_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('company_division/new.html.twig', [
            'company_division' => $companyDivision,
            'form' => $form->createView(),
        ]);
    }





    
    /**
     * @Route("/this/new", name="this_company_division_new", methods={"GET","POST"})
     */
    public function thisNew(Request $request): Response
    {
        $companyDivision = new CompanyDivision();
        $form = $this->createForm(CompanyDivisionType::class, $companyDivision);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($companyDivision);
            $entityManager->flush();

            return $this->redirectToRoute('company_division_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('company_division/new.html.twig', [
            'company_division' => $companyDivision,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="company_division_show", methods={"GET"})
     */
    public function show(CompanyDivision $companyDivision): Response
    {
        return $this->render('company_division/show.html.twig', [
            'company_division' => $companyDivision,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="company_division_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CompanyDivision $companyDivision): Response
    {
        $form = $this->createForm(CompanyDivisionType::class, $companyDivision);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('company_division_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('company_division/edit.html.twig', [
            'company_division' => $companyDivision,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="company_division_delete", methods={"POST"})
     */
    public function delete(Request $request, CompanyDivision $companyDivision): Response
    {
        if ($this->isCsrfTokenValid('delete'.$companyDivision->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($companyDivision);
            $entityManager->flush();
            
        }

        return $this->redirectToRoute('company_division_index', [], Response::HTTP_SEE_OTHER);
    }
}
