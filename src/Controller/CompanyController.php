<?php

namespace App\Controller;

use App\Entity\Company;
use App\Form\CompanyType;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route(
 *     "/{_locale}/company",
 *     requirements={
 *         "_locale": "en|fr|de",
 *     }
 * )
 * @IsGranted("IS_AUTHENTICATED_FULLY") 
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

            if (!empty($form['banner'])) {
                $files = $form['banner']->getData();
                $this->upload($company, $files,'banner');
            }

            if (!empty($form['logo'])) {
                $files = $form['logo']->getData();
                $this->upload($company, $files,'banner');
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
     * @Route("/this_new", name="this_company_new", methods={"GET","POST"})
     */
    public function thisNew(Request $request): Response
    {
        $company = new Company();
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($company);
            $entityManager->flush();

            if (!empty($form['banner'])) {
                $files = $form['banner']->getData();
                $this->upload($company, $files,'banner');
            }

            if (!empty($form['logo'])) {
                $files = $form['logo']->getData();
                $this->upload($company, $files,'banner');
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
            $this->dirRenamer($code, $form['code']->getdata());

            try {
                $this->dirRenamer($code, $form['code']->getdata());
            } catch (\Throwable $th) {
                //throw $th;
                die('un probleme est suvenu');
            }
            $logo = $form['logo']->getData();
            $banner = $form['banner']->getData();
            $this->upload($company, $logo,'logo');
            $this->upload($company, $banner,'banner');

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




    public function upload(Company $company, $file,$type)
    {
        $path = './logo_and_banner/';
        if(!empty($file))
        {
            $name = $type.'_'.$company->getCode() . '.' . $file->guessExtension();
            $file->move($path, $name);
            $method = 'set'.ucfirst($type);
            $company -> $method($name);
    
    

        }
       $em =  $this->getDoctrine()->getManager();
       $em->persist($company);
       $em->flush();
    }




    public function dirMaker(Company $company)
    {
        if (!is_dir('./' . $_ENV['DATA_DIR'] . $company->getCode())) {
            mkdir('./' . $_ENV['DATA_DIR'] . $company->getCode());
        } else {
            echo "ce dossier existe déja ";
        }
    }
    
    
    
    
    
    public function dirRenamer(string $code, $newDirName)
    {
        if (is_dir('./' . $_ENV['DATA_DIR'] . $code)) {
            rename('./' . $_ENV['DATA_DIR'] . $code, './' . $_ENV['DATA_DIR'] . $newDirName);
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
