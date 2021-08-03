<?php

namespace App\Controller;

use App\Entity\Directory;
use App\Form\DirectoryType;
use App\Repository\FileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/directory")
 */
class DirectoryController extends AbstractController
{
    /**
     * @Route("/", name="directory_index", methods={"GET"})
     */
    public function index(FileRepository $fileRepository): Response
    {
        return $this->render('directory/index.html.twig', [
            'directories' => $fileRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="directory_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $directory = new Directory();
        $form = $this->createForm(DirectoryType::class, $directory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($directory);
            $entityManager->flush();

            return $this->redirectToRoute('directory_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('directory/new.html.twig', [
            'directory' => $directory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="directory_show", methods={"GET"})
     */
    public function show(Directory $directory): Response
    {
        return $this->render('directory/show.html.twig', [
            'directory' => $directory,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="directory_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Directory $directory): Response
    {
        $form = $this->createForm(DirectoryType::class, $directory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('directory_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('directory/edit.html.twig', [
            'directory' => $directory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="directory_delete", methods={"POST"})
     */
    public function delete(Request $request, Directory $directory): Response
    {
        if ($this->isCsrfTokenValid('delete'.$directory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($directory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('directory_index', [], Response::HTTP_SEE_OTHER);
    }
}
