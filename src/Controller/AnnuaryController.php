<?php

namespace App\Controller;

use App\Entity\Annuary;
use App\Form\AnnuaryType;
use App\Repository\AnnuaryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;



/**
 * @Route(
 *     "/{_locale}/annuary",
 *     requirements={
 *         "_locale": "en|fr|de",
 *     }
 *)
 * @IsGranted("IS_AUTHENTICATED_FULLY") 
 * 
 */
class AnnuaryController extends AbstractController
{
    /**
     * @Route("/", name="annuary_index", methods={"GET"})
     */
    public function index(AnnuaryRepository $annuaryRepository): Response
    {
        return $this->render('annuary/index.html.twig', [
            'annuaries' => $annuaryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="annuary_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $annuary = new Annuary();
        $form = $this->createForm(AnnuaryType::class, $annuary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($annuary);
            $entityManager->flush();

            return $this->redirectToRoute('annuary_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('annuary/new.html.twig', [
            'annuary' => $annuary,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="annuary_show", methods={"GET"})
     */
    public function show(Annuary $annuary): Response
    {
        return $this->render('annuary/show.html.twig', [
            'annuary' => $annuary,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="annuary_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Annuary $annuary): Response
    {
        $form = $this->createForm(AnnuaryType::class, $annuary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('annuary_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('annuary/edit.html.twig', [
            'annuary' => $annuary,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="annuary_delete", methods={"POST"})
     */
    public function delete(Request $request, Annuary $annuary): Response
    {
        if ($this->isCsrfTokenValid('delete' . $annuary->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($annuary);
            $entityManager->flush();
        }

        return $this->redirectToRoute('annuary_index', [], Response::HTTP_SEE_OTHER);
    }
}
