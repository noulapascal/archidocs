<?php

namespace App\Controller;

use App\Entity\Directory;
use App\Form\DirectoryType;
use App\Repository\DirectoryRepository;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Company;
use App\Entity\CompanyDivision;
use App\Form\UploadType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/directory")
 * @IsGranted("IS_AUTHENTICATED_FULLY") */
class DirectoryController extends AbstractController
{
    /**
     * @Route("/", name="directory_index", methods={"GET"})
     */
    public function index(DirectoryRepository $directoryRepository): Response
    {

        $currentUser = $this->getUser();
        if(!empty($currentUser)){
            $company = $currentUser->getDivision()->getCompany();
            $directories = $directoryRepository->findByCompanyDivision($currentUser->getDivision());
        }
        return $this->render('directory/index.html.twig', [
            'directories' => $directories,
        ]);
    }

    /**
     * @Route("/company/{id}", name="directory_company_index", methods={"GET"})
     */
    public function indexCompany(DirectoryRepository $directoryRepository, Company $company): Response
    {
        /**
         *@param companyDivision $value 
         */
        
        $divisions = $company->getCompanyDivisions();
        foreach ($divisions as $key => $value) {
            # code...
            $dir[] = $value->getFolders();
        }
        return $this->render('directory/index2.html.twig', [
            'directories' => $dir,
        ]);
    }



    /**
     * @Route("/file_upload/{id}", name="RouteName", methods={"GET","POST"})
     */
    public function uploadFile(Request $request,Directory $directory): Response
    {
        
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $files = $form['files']->getData();
            $this->upload($directory,$files);
            return $this->redirectToRoute('index_list_file',[
                'id'=>$directory->getId()
            ]);

        }

        return $this->render('directory/new.html.twig', [
            'form'=>$form->createView(),
        ]);
    }


    public function upload(Directory $directory, array $files){
        foreach ($files as $key => $file) {
            $path = $directory->getPath().'/';
            $name = $file->getClientOriginalName();
            $file->move($path,$name);

        }
    }

    /**
     * @Route("/company/division/{id}", name="directory_company_division_index", methods={"GET"})
     */
    public function indexCompanyDivision(DirectoryRepository $directoryRepository, CompanyDivision $companyDivision): Response
    {

       $dir = $companyDivision->getFolders(); 
        return $this->render('directory/index.html.twig', [
            'directories' => $dir,
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
            $path = './'.$directory->getName();


            try {
                //code...
                $dir = mkdir('./'.$directory->getCompanyDivision()[0]->getCompany()->getCode().'/'.$path);


            } catch (\Throwable $th) {
                //throw $th;
                die('echec de la création du repertoire'.' <a class="btn btn-link"  href="'.$this->generateUrl('directory_index').'">revenir</a>');
            }


            try {
                //code...
                $directory = $this->addScheme($directory);


            } catch (\Throwable $th) {
                rmdir($path);
                die('echec de la sauvegarde du repertoire'.' <a class="btn btn-link"  href="'.$this->generateUrl('directory_index').'">revenir</a>');

            }
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
            $newpath = './'.$directory->getCompanyDivision()[0]->getCompany()->getCode().'/'.$directory->getName();
            $oldpath = $directory->getCompanyDivision()[0]->getCompany()->getCode().$directory->getPath();
            $path = './'.$directory->getCompanyDivision()[0]->getCompany()->getCode().'/'.$directory->getName();
            try {
                rename($oldpath,$newpath);

            } catch (\Throwable $th) {
                //throw $th;
                die('echec de la modification du repertoire'.' <a class="btn btn-link"  href="'.$this->generateUrl('directory_index').'">revenir</a>');

            }
            $directory->setPath($path);

            
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('directory_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('directory/edit.html.twig', [
            'directory' => $directory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/del/{id}", name="directory_delete", methods={"POST"})
     */
    public function delete(Request $request, Directory $directory): Response
    {
        if ($this->isCsrfTokenValid('delete'.$directory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
                            rmdir('./'.$directory->getCompanyDivision()[0]->getCompany()->getCode().'/'.$directory->getPath());

            try {
                
                rmdir('./'.$directory->getCompanyDivision()[0]->getCompany()->getCode().'/'.$directory->getPath());
            } catch (\Throwable $th) {
                //throw $th;
                die('une erreur est apparue');
            }
            $entityManager->remove($directory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('directory_index', [], Response::HTTP_SEE_OTHER);
    }
/**
 * @param Directory $dir 
 */
    
public function addScheme(Directory $dir) {

    $entry = $dir->getCompanyDivision()[0]->getCompany()->getCode().'/'.$dir->getName();
    //$t = explode(".", $entry);
    $dir->settype(filetype($entry));
     $dir->setSize(filesize($entry))
     ->setPath($entry)
     ->setPermissions(fileperms($entry));
     //->setExtension($t[count($t)-1]);
    return $dir;
  }

 
  public function addScheme2($entry, $base, $type)
  {
    $tab['name'] = $entry;
    $tab['type'] = filetype($base . "/" . $entry);
    $tab['date'] = filemtime($base . "/" . $entry);
    $tab['size'] = filesize($base . "/" . $entry);
    $tab['perms'] = fileperms($base . "/" . $entry);
    $tab['access'] = fileatime($base . "/" . $entry);
    $t = explode(".", $entry);
    $tab['ext'] = $t[count($t) - 1];
    return $tab;
  }
  

/**
 * @Route("/list/{id<\d+>}", name="index_list_file", methods = {"GET"})
 */
 public function indexFileList(Directory $directory): Response
    {

        $data=$this->listFile($directory);

        return $this->render('directory/index3.html.twig', [
            'directories' => $data,
            'parent' => $directory
        ]);
    }




    /**
 * @Route("/folderlist/{id}", name="index_list_folder", methods = {"GET"})
 */
 public function FolderFileList(Directory $directory): Response
 {

    $data = $directory->getChildren();
    // $data=$this->listFile2($parent.'/'.$name);

     return $this->render('directory/indexListFile.html.twig', [
         'directories' => $data,
         'parent' => $directory
     ]);
 }






    public function listFile(Directory $directory)
    {
        $namedir = $directory->getPath();
        $dir = opendir($namedir);

        while ($file = readdir($dir)) {
            if (is_dir($namedir . "/" . $file)) {
              if (!in_array($file, array(".", ".."))) {
                $tab_dir[] = $this->addScheme2($file, $namedir, 'dir');
              }
            } else {
              $tab_file[] = $this->addScheme2($file, $namedir, 'file');
            }
        }
        return [
            "folders" => !empty($tab_dir)?$tab_dir:[],
            "files" => !empty($tab_file)?$tab_file:[],
            'parent' => $directory

        ];
    }


    public function listFile2($namedir)
    {
        $dir = opendir($namedir);

        while ($file = readdir($dir)) {
            if (is_dir($namedir . "/" . $file)) {
              if (!in_array($file, array(".", ".."))) {
                $tab_dir[] = $this->addScheme2($file, $namedir, 'dir');
              }
            } else {
              $tab_file[] = $this->addScheme2($file, $namedir, 'file');
            }
        }
        return [
            "folders" => !empty($tab_dir)?$tab_dir:[],
            "files" => !empty($tab_file)?$tab_file:[],
            'parent' => $directory

        ];
    }

    


    
    /**
     * @Route("/child/new/{id}", name="directory_child_new", methods={"GET","POST"})
     */
    public function Childnew(Request $request, Directory $parent): Response
    {
        $directory = new Directory();
        $form = $this->createForm(DirectoryType::class, $directory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $path = './'.$parent->getPath().'/'.$directory->getName();
          //  $dir = mkdir('./'.$parent->getCompanyDivision()[0]->getCompany()->getCode().'/'.$path);
            $dir = mkdir('./'.$parent->getCompanyDivision()[0]->getCompany()->getCode().'/'.$path);



            try {
                //code...


            } catch (\Throwable $th) {
                //throw $th;
                die('echec de la création du repertoire'.' <a class="btn btn-link"  href="'.$this->generateUrl('directory_index').'">revenir</a>');
            }


            try {
                //code...
                $directory = $this->addScheme($directory);


            } catch (\Throwable $th) {
                rmdir('./'.$directory->getCompanyDivision()[0]->getCompany()->getCode().'/'.$path);
                die('echec de la sauvegarde du repertoire'.' <a class="btn btn-link"  href="'.$this->generateUrl('directory_index').'">revenir</a>');

            }
            $entityManager->persist($directory);
            $entityManager->flush();

            return $this->redirectToRoute('directory_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('directory/new.html.twig', [
     
            'directory' => $directory,
            'form' => $form->createView(),
        ]);
    }

}
