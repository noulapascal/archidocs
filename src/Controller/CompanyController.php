<?php

namespace App\Controller;

use App\Entity\Company;
use App\Form\CompanyType;
use App\Repository\CompanyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/company")
 */
class CompanyController extends AbstractController
{
    /**
     * @Route("/", name="company_index", methods={"GET"})
     */
    public function index(CompanyRepository $companyRepository): Response
    {
        return $this->render('company/index.html.twig', [
            'companies' => $companyRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="company_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $company = new Company();
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($company);
            $entityManager->flush();

            if (!empty($form['file'])) {
                $files = $form['file']->getData();
                $this->upload($directory, $files);
            }


            $this->dirMaker($company);
            return $this->redirectToRoute('company_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('company/new.html.twig', [
            'company' => $company,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id<\d+>}", name="company_show", methods={"GET"})
     */
    public function show(Company $company): Response
    {
        return $this->render('company/show.html.twig', [
            'company' => $company,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="company_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Company $company): Response
    {
        $code = $company->getCode();
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $this->dirRenamer($code, $form['code']->getdata());
            } catch (\Throwable $th) {
                //throw $th;
                die('un probleme est suvenu');
            }
            $logo = $form['logo']->getData();
            $banner = $form['banner']->getData();
            $this->upload($company, $logo);
            $this->upload($company, $banner);

            $this->getDoctrine()->getManager()->flush();


            return $this->redirectToRoute('company_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('company/edit.html.twig', [
            'company' => $company,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="company_delete", methods={"POST"})
     */
    public function delete(Request $request, Company $company): Response
    {
        if ($this->isCsrfTokenValid('delete' . $company->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            try {
                //code...
                $this->dirRemover($company);
            } catch (\Throwable $th) {
                //throw $th;
            }
            /*
            foreach ($company->getCompanyDivisions() as $key => $value) {
                # code...
                $company->removeCompanyDivision($value);
            }
            */
            $entityManager->remove($company);
            $entityManager->flush();
        }

        return $this->redirectToRoute('company_index', [], Response::HTTP_SEE_OTHER);
    }




    public function upload(Company $company, $file)
    {
        $path = './logo_and_banner/' . $company->getCode();
        $name = $company->getCode() . '.' . $file->guessExtension();
        $file->move($path, $name);
    }




    public function dirMaker(Company $company)
    {
        if (!is_dir('./' . $_ENV['DATA_DIR'] . $company->getCode())) {
            mkdir('./' . $_ENV['DATA_DIR'] . $company->getCode());
        } else {
            echo "ce dossier existe déja ";
        }
    }
    
    
    
    
    
    public function dirRenamer(string $company, $newDirName)
    {
        if (is_dir('./' . $_ENV['DATA_DIR'] . $company)) {
            rename('./' . $_ENV['DATA_DIR'] . $company, './' . $_ENV['DATA_DIR'] . $newDirName);
        } else {
            echo "ce dossier n'existe déja plus";
            //   mkdir('./' . $_ENV['DATA_DIR'] . $company->getCode());
        }
    }
    public function dirRemover(Company $company)
    {
        if (is_dir('./' . $_ENV['DATA_DIR'] . $company->getCode())) {
            rmdir('./' . $_ENV['DATA_DIR'] . $company->getCode());
        } else {
            echo "ce dossier n'existe déja plus";
        }
    }
}
