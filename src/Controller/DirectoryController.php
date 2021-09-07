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
 * @Route(
 *     "/{_locale}/directory",
 *     requirements={
 *         "_locale": "en|fr|de",
 *     }
 * ) 
 * @IsGranted("IS_AUTHENTICATED_FULLY") 
 */
class DirectoryController extends AbstractController
{
    /**
     * @Route("/", name="directory_index", methods={"GET"})
     */
    public function index(DirectoryRepository $directoryRepository): Response
    {

        $currentUser = $this->getUser();
        if (!empty($currentUser)) {


            $company = $currentUser->getDivision()->getCompany();
            $directories = $directoryRepository->findByCompanyDivisionWithNoParent($currentUser->getDivision());
            $specialDir = $directoryRepository->findBySpecialAccess(
                $this->getUser()
            );
        }

        return $this->render('directory/index.html.twig', [
            'directories' => $directories,
            'specialDir' => !empty($specialDir)? $specialDir : []
        ]);
    }



        
    /**
     * @Route("/search", name="directory_search", methods={"GET","POST"})
     */
    public function searchDir(DirectoryRepository $directoryRepository, Request $request ): Response
    {

        $currentUser = $this->getUser();
        if (!empty($currentUser)) {


            $keyword = $request->get('keyword');
            $company = $currentUser->getDivision()->getCompany();
            $directories = $directoryRepository->findByKeyword($keyword,$currentUser->getDivision());
            //var_dump($directories);

        }       
        

        return $this->render('directory/index.html.twig', [
            'directories' => !empty($directories)? $directories: [],
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
            // $dir[$value->getName()] = $value->getFolders();
        }
        $dir = $directoryRepository->findByCompanyWithNoParent($company);
        return $this->render('directory/indexCompany.html.twig', [
            'directories' => $dir,
        ]);
    }



    /**
     * @Route("/file_upload/{id}", name="file_upload", methods={"GET","POST"})
     */
    public function uploadFile(Request $request, Directory $directory): Response
    {

        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $files = $form['files']->getData();
            $this->upload($directory, $files);
            return $this->redirectToRoute('index_list_file', [
                'id' => $directory->getId()
            ]);
        }

        return $this->render('directory/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    public function upload(Directory $directory, array $files)
    {

        $namedir = $_ENV['DATA_DIR'] . $directory->getCompanyDivision()[0]->getCompany()->getCode() . '/' . $directory->getPath();

        if (is_dir($namedir)) {
            $dir = opendir($namedir);
        } 
        else
        {

            $path = $_ENV['DATA_DIR'] . $directory->getCompanyDivision()[0]->getCompany()->getCode();
            $parent = $directory->getParent();
            while ($parent) {
                $names[] = '/' . $parent->getName();
                $parent = $parent->getParent();
            }
            $names_reverse = array_reverse($names);
            foreach ($names_reverse as $key => $name) {
                # code...
                $path .= $name;
            }
            $path .= '/' . $directory->getName();
            if (is_dir($path))
                $namedir = $path;
        }
        foreach ($files as $key => $file) {
            
            $path = './' . $directory->getCompanyDivision()[0]->getCompany()->getCode() . '/' . $directory->getPath() . '/';
            $name = $file->getClientOriginalName();
            $file->move($namedir, $name);

        }
    }

    /**
     * @Route("/company/division/{id}", name="directory_company_division_index", methods={"GET"})
     */
    public function indexCompanyDivision(DirectoryRepository $directoryRepository, CompanyDivision $companyDivision): Response
    {

        $dir = $directoryRepository->findByCompanyDivisionWithNoParent($companyDivision);
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
            $path = './' . $directory->getName();



            try {
                //code...
                $this->dirMaker($directory);
            } catch (\Throwable $th) {
                //throw $th;
                die('echec de la création du repertoire' . ' <a class="btn btn-link"  href="' . $this->generateUrl('directory_index') . '">revenir</a>');
            }


            try {
                //code...
                $directory = $this->addScheme($directory);
            } catch (\Throwable $th) {
                $this->dirRemover($directory);
                die('echec de la sauvegarde du repertoire' . ' <a class="btn btn-link"  href="' . $this->generateUrl('directory_index') . '">revenir</a>');
            }
            $directory->setAuthor($this->getUser());
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
            $newName =  $form->get('name')->getData();
            $oldpath = './' . $directory->getCompanyDivision()[0]->getCompany()->getCode() . '/' . $directory->getPath();
            //$path = './'.$directory->getCompanyDivision()[0]->getCompany()->getCode().'/'.$directory->getName();
            // rename($oldpath, $newpath);

            $this->dirRenamer($directory, $newName);

            try {
            } catch (\Throwable $th) {
                //throw $th;
                die('echec de la modification du repertoire' . ' <a class="btn btn-link"  href="' . $this->generateUrl('directory_index') . '">revenir</a>');
            }
            if ($directory->getParent()) {
                $newPath = $directory->getParent()->getPath() . '/' . $directory->getName();
            } else {
                $newPath = $directory->getName();
            }
            $directory->setPath($newPath);


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
        if ($this->isCsrfTokenValid('delete' . $directory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            $this->dirRemover($directory);

            try {
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

    public function addScheme(Directory $directory)
    {

        if (!empty($directory->getPath())) {
            $entry =  $_ENV['DATA_DIR'] . $directory->getCompanyDivision()[0]->getCompany()->getCode() . $directory->getPath();
        } else {
            $entry =   $_ENV['DATA_DIR'] . $directory->getCompanyDivision()[0]->getCompany()->getCode() . '/' . $directory->getName();
            $directory->setPath('/' . $directory->getName());
        }

        if (is_dir($entry)) {
            $dir = opendir($entry);
        } else {

            $path = $this->FolderTruePath($directory);

            if (is_dir($path)) {
                $dir = opendir($path);
                $entry = $path;
            } else {
                mkdir($path);
                $entry = $path;
            }
        }

        //$t = explode(".", $entry);
        $directory->setSize(filesize($entry))
            ->setPermissions(fileperms($entry))
            ->setType(filetype($entry));

        //->setExtension($t[count($t)-1]);
        return $directory;
    }


    public function addScheme2($entry, $type, Directory $directory)
    {

        $namedir = $_ENV['DATA_DIR'] . $directory->getCompanyDivision()[0]->getCompany()->getCode() . '/' . $directory->getPath();

        if (is_dir($namedir)) {
            $dir = opendir($namedir);
        } else {

            $path = $_ENV['DATA_DIR'] . $directory->getCompanyDivision()[0]->getCompany()->getCode();
            $parent = $directory->getParent();
            $dirPath = '';
            while ($parent) {
                $names[] = '/' . $parent->getName();
                $parent = $parent->getParent();
            }
            $names_reverse = array_reverse($names);
            foreach ($names_reverse as $key => $name) {
                # code...
                $path .= $name;
                $dirPath .= $name;
            }
            $path .= '/' . $directory->getName();
            $dirPath .= '/' . $directory->getName();
            if (is_dir($path))
                $namedir = $path;
            $directory->setPath($dirPath);
        }
        //var_dump($namedir);

        $tab['name'] = $entry;
        $tab['type'] = filetype($namedir . "/" . $entry);
        $tab['date'] = filemtime($namedir . "/" . $entry);
        $tab['size'] = filesize($namedir . "/" . $entry);
        $tab['perms'] = fileperms($namedir . "/" . $entry);
        $tab['access'] = fileatime($namedir . "/" . $entry);
        $tab['path'] = $namedir . "/" . $entry;

        $t = explode(".", $entry);
        $tab['ext'] = $t[count($t) - 1];
        $tab['description'] = $this->assocExt($tab['ext']);

        return $tab;
    }


    /**
     * @Route("/list/{id<\d+>}", name="index_list_file", methods = {"GET"})
     */
    public function indexFileList(Directory $directory, DirectoryRepository $directoryRepository): Response
    {


        return $this->render('directory/index3.html.twig', [
            'data' =>$directory->getChildrenFiles(),
            'specialDir' => $directoryRepository->findBySpecialAccess($this->getUser()),
            'directories' => $directory->getChildren(),
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
        $namedir = $_ENV['DATA_DIR'] . $directory->getCompanyDivision()[0]->getCompany()->getCode() . '/' . $directory->getPath();

        if (is_dir($namedir)) {
            $dir = opendir($namedir);
        } else {

            $path = $this->FolderTruePath($directory);
            $namedir = $path;
            $dir = opendir($namedir);
        }


        while ($file = readdir($dir)) {
            if (is_dir($namedir . "/" . $file)) {
                if (!in_array($file, array(".", ".."))) {
                    $tab_dir[] = $this->addScheme2($file, 'dir', $directory);
                }
            } else {
                $tab_file[] = $this->addScheme2($file, 'file', $directory);
            }
        }
        return [
            //  "folders" => !empty($tab_dir) ? $tab_dir : [],
            "files" => !empty($directory->getChildrenFiles())? $directory->getChildrenFiles() : [],
            'parent' => $directory

        ];
    }


    public function listFile2(Directory $directory)
    {
        $namedir = $_ENV['DATA_DIR'] . $directory->getCompanyDivision()[0]->getCompany()->getCode() . '/' . $directory->getPath();

        if (is_dir($namedir)) {
            $dir = opendir($namedir);
        } else {

            $path = $_ENV['DATA_DIR'] . $directory->getCompanyDivision()[0]->getCompany()->getCode();
            $parent = $directory->getParent();
            if (!empty($parent)) {
                while ($parent) {
                    $names[] = '/' . $parent->getName();
                    $parent = $parent->getParent();
                }
                $names_reverse = array_reverse($names);
                foreach ($names_reverse as $key => $name) {
                    # code...
                    $path .= $name;
                }
            }

            $path .= '/' . $directory->getName();
            if (is_dir($path))
                $namedir = $path;
        }

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
            "folders" => !empty($tab_dir) ? $tab_dir : [],
            "files" => !empty($tab_file) ? $tab_file : [],
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
            $path = '/' . $parent->getPath() . '/' . $directory->getName();
            $directory->addCompanyDivision($this->getUser()->getDivision());
            $directory->setParent($parent);

            //  $dir = mkdir('./'.$parent->getCompanyDivision()[0]->getCompany()->getCode().'/'.$path);

            try {
                //code...
                $this->dirMaker($directory);
                $directory->setPath($path);
                $directory->setParent($parent);
            } catch (\Throwable $th) {
                //throw $th;
                die('echec de la création du repertoire' . ' <a class="btn btn-link"  href="' . $this->generateUrl('directory_index') . '">revenir</a>');
            }


            try {
                //code...
                $directory = $this->addScheme($directory);
            } catch (\Throwable $th) {
                rmdir($directory->getCompanyDivision()[0]->getCompany()->getCode() . '/' . $path);
                die('echec de la sauvegarde du repertoire' . ' <a class="btn btn-link"  href="' . $this->generateUrl('directory_index') . '">revenir</a>');
            }





            $entityManager->persist($directory);
            $entityManager->flush();

            return $this->redirectToRoute('index_list_file', ['id' => $parent->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('directory/newChild.html.twig', [

            'directory' => $directory,
            'form' => $form->createView(),
            'parent' => $parent
        ]);
    }

    public function dirMaker(Directory $directory)
    {
        $name = $directory->getName();
        $path = $directory->getPath();
        $parent = $directory->getParent();
        $companyCode =  $directory->getCompanyDivision()[0]->getCompany()->getCode();
        if (!empty($parent)) {
            $dirName = './' . $_ENV['DATA_DIR'] . $companyCode .
                '/' . $parent->getPath() . '/' . $directory->getName();
        } else {
            $dirName = './' . $_ENV['DATA_DIR'] . $companyCode . '/' . $directory->getName() . '/';
        }
        try {
            //code...
            mkdir($dirName);
        } catch (\Throwable $th) {
            //throw $th;
            echo 'impossible de créér le fichier';
        }
    }


    public function dirRenamer(Directory $directory, $newName)
    {


        $name = $directory->getName();
        $path = $directory->getPath();
        $parent = $directory->getParent();
        $companyCode =  $directory->getCompanyDivision()[0]->getCompany()->getCode();
        $dirName = './' . $_ENV['DATA_DIR'] . $companyCode . '/' . $directory->getPath();

        if (is_dir($dirName)) {
            $parent = $directory->getParent();
            if (!empty($parent)) {
                $newPath =  './' . $_ENV['DATA_DIR'] . $companyCode . '/' .
                    $parent->getPath() . '/' . $newName;
                //$dirPath = $parent->getPath() . '/' . $newName;
                // $directory->setPath($dirPath);
            } else {
                $newPath =  './' . $_ENV['DATA_DIR'] . $companyCode . '/' . $newName;
            }
            rename($dirName, $newPath);

            //    rename($dirName, $newPath);

        } else {
            echo "ce dossier n'existe déja plus";
            //mkdir($dirName);
            //$this->dirRenamer($directory,$newName);
        }
    }
    public function dirRemover(Directory $directory)
    {

        $dirName = $this->FolderTruePath($directory);
        if (is_dir($dirName)) {
            rmdir($dirName);
        } else {
            echo "ce dossier n'existe déja plus";
        }
    }





    public function FolderTruePath($directory)
    {
        $name = $directory->getName();
        $path = $directory->getPath();
        $parent = $directory->getParent();
        $companyCode =  $directory->getCompanyDivision()[0]->getCompany()->getCode();
        $dirName = './' . $_ENV['DATA_DIR'] . $companyCode . '/' . $directory->getPath();
        /*
        if (is_dir($dirName)) {
            $parent = $directory->getParent();
            if (!empty($parent)) {
                $newPath =  './' . $_ENV['DATA_DIR'] . $companyCode .
                    $parent->getPath() . '/' . $newName;
                //$dirPath = $parent->getPath() . '/' . $newName;
                // $directory->setPath($dirPath);
            } else {
                $newPath =  './' . $_ENV['DATA_DIR'] . $companyCode . '/' . $newName;
            }
            rename($dirName, $newPath);

            //    rename($dirName, $newPath);

        } else {
            echo "ce dossier n'existe déja plus";
            //mkdir($dirName);
            //$this->dirRenamer($directory,$newName);
        }

        */




        //   if (is_dir($dirName)) {
        // $dir = opendir($dirName);
        //$dirName = $path;
        // } else
        if (!is_dir($dirName)) {


            if (!empty($parent)) {

                $path = $_ENV['DATA_DIR'] . $directory->getCompanyDivision()[0]->getCompany()->getCode();
                $parent = $directory->getParent();
                while ($parent) {
                    $names[] = '/' . $parent->getName();
                    $parent = $parent->getParent();
                }
                $names_reverse = array_reverse($names);
                foreach ($names_reverse as $key => $name) {
                    # code...
                    $path .= '/' . $name;
                }
                $path .= '/' . $directory->getName();
                if (is_dir($path)) {
                    //  $dir = opendir($path);
                    $dirName = $path;
                } else {
                    //mkdir($path);
                    $dirName = $path;
                }
            }
        }


        return $dirName;
    }





    public function assocExt($ext)
    {
        $e = array(
            '' => "inconnu",
            'doc' => "Microsoft Word",
            'xls' => "Microsoft Excel",
            'ppt' => "Microsoft Power Point",
            'pdf' => "Adobe Acrobat",
            'zip' => "Archive WinZip",
            'txt' => "Document texte",
            'gif' => "Image GIF",
            'jpg' => "Image JPEG",
            'png' => "Image PNG",
            'php' => "Script PHP",
            'php3' => "Script PHP",
            'mp3' => "audio mp3",
            'mp4' => "video/audio mp3",
            'htm' => "Page web",
            'html' => "Page web",
            'css' => "Feuille de style",
            'js' => "JavaScript",
            "Extension"  =>  "Type de document",
            ".aac"  =>  "fichier audio AAC",
            ".abw"  =>  "document AbiWord",
            ".arc"  =>  "archive (contenant plusieurs fichiers)",
            ".avi"  =>  "AVI : Audio Video Interleave",
            ".azw"  =>  "format pour eBook Amazon Kindle",
            ".bin"  =>  "n'importe quelle donnée binaire",
            ".bmp"  =>  "Images bitmap Windows OS/2",
            ".bz"  =>  "archive BZip",
            ".bz2"  =>  "archive BZip2",
            ".csh"  =>  "script C-Shell",
            ".css"  =>  "fichier Cascading Style Sheets (CSS)",
            ".csv"  =>  "fichier Comma-separated values (CSV)",
            ".doc"  =>  "Microsoft Word",
            ".docx"  =>  "Microsoft Word (OpenXML)",
            ".eot"  =>  "police MS Embedded OpenType",
            ".epub"  =>  "fichier Electronic publication (EPUB)",
            ".gif"  =>  "fichier Graphics Interchange Format (GIF)",
            ".htm" =>  "fichier HyperText Markup Language (HTML)",
            ".html"  =>  "fichier HyperText Markup Language (HTML)",
            ".ico"  =>  "icône",
            ".ics"  =>  "élément iCalendar",
            ".jar"  =>  "archive Java (JAR)",
            ".jpeg" => "image JPEG",
            ".jpg"  =>  "image JPEG",
            ".js"  =>  "JavaScript (ECMAScript)",
            ".json"  =>  "donnée au format JSON",
            ".mid" =>  "fichier audio Musical Instrument Digital Interface (MIDI)",
            ".midi"  =>  "fichier audio Musical Instrument Digital Interface (MIDI)",
            ".mpeg"  =>  "vidéo MPEG",
            ".mpkg"  =>  "paquet Apple Installer",
            ".odp"  =>  "présentation OpenDocument",
            ".ods"  =>  "feuille de calcul OpenDocument",
            ".odt"  =>  "document texte OpenDocument",
            ".oga"  =>  "fichier audio OGG",
            ".ogv"  =>  "fichier vidéo OGG",
            ".ogx"  =>  "OGG",
            ".otf"  =>  "police OpenType",
            ".png"  =>  "fichier Portable Network Graphics",
            ".pdf"  =>  "Adobe Portable Document Format (PDF)",
            ".ppt"  =>  "présentation Microsoft PowerPoint",
            ".pptx"  =>  "présentation Microsoft PowerPoint (OpenXML)",
            ".rar"  =>  "archive RAR",
            ".rtf"  =>  "Rich Text Format (RTF)",
            ".sh"  =>  "script shell",
            ".svg"  =>  "fichier Scalable Vector Graphics (SVG)",
            ".swf"  =>  "fichier Small web format (SWF) ou Adobe Flash",
            ".tar"  =>  "fichier d'archive Tape Archive (TAR)",
            ".tif"   =>  "image au format Tagged Image File Format (TIFF)",
            ".tiff"  =>  "image au format Tagged Image File Format (TIFF)",
            ".ts"  =>  "fichier Typescript",
            ".ttf"  =>  "police TrueType",
            ".vsd"  =>  "Microsoft Visio",
            ".wav"  =>  "Waveform Audio Format",
            ".weba"  =>  "fichier audio WEBM",
            ".webm"  =>  "fichier vidéo WEBM",
            ".webp"  =>  "image WEBP",
            ".woff"  =>  "police Web Open Font Format (WOFF)",
            ".woff2"  =>  "police Web Open Font Format (WOFF)",
            ".xhtml"  =>  "XHTML",
            ".xls"  =>  "Microsoft Excel",
            ".xlsx"  =>  "Microsoft Excel (OpenXML)",
            ".xml"  =>  "XML",
            ".xul"  =>  "XUL",
            ".zip"  =>  "archive ZIP",
            ".3gp"  =>  "conteneur audio/vidéo 3GPP",
            ".3g2"  =>  "conteneur audio/vidéo 3GPP2",
            ".7z"  =>  "archive 7-zip",
            ".dwg" => "Fichiers AutoCAD (d'après NCSA)",
            ".asd" => "Fichiers Astound",
            ".asn" => "Fichiers Astound",
            ".tsp" => "Fichiers TSP",
            ".dxf" => "Fichiers AutoCAD (d'après CERN)",
            ".spl" => "Fichiers Flash Futuresplash",
            ".gz" => "Fichiers GNU Zip",
            ".ptlk" => "Fichiers Listenup",
            ".hqx" => "Fichiers binaires Macintosh",
            ".mbd" => "Fichiers Mbedlet",
            ".mif" => "Fichiers FrameMaker Interchange Format",
            ".xls" => "Fichiers Microsoft Excel",
            ".xla" => "Fichiers Microsoft Excel",
            ".hlp" => "Fichiers Microsoft Excel",
            ".chm" => "Fichiers d'aide Microsoft Windows",
            ".ppt"  => "Fichiers Microsoft Powerpoint",
            ".ppz"  => "Fichiers Microsoft Powerpoint",
            ".pps"  => "Fichiers Microsoft Powerpoint",
            ".pot" => "Fichiers Microsoft Powerpoint",
            ".doc" => "Fichiers Microsoft Word",
            ".dot" => "Fichiers Microsoft Word",
            ".bin"  => "Fichiers exécutables",
            ".exe"  => "Fichiers exécutables",
            ".com" => "Fichiers exécutables",
            ".dll"  => "Dynamic link library",
            ".class" => "Fichiers de classe Java",
            ".oda" => "Fichiers Oda",
            ".pdf" => "Fichiers Adobe PDF",
            ".ai" => "Fichiers Adobe Postscript",
            ".eps"  => "Fichiers Adobe Postscript",
            ".ps" => "Fichiers Adobe Postscript",
            ".rtc" => "Fichiers RTC",
            ".rtf" => "Fichiers Microsoft RTF",
            ".smp" => "Fichiers Studiom",
            ".tbk" => "Fichiers Toolbook",
            ".vmd" => "Fichiers Vocaltec Mediadesc",
            ".vmf" => "Fichiers Vocaltec Media",
            ".bcpio" => "Fichiers BCPIO",
            ".z" => "Fichiers -",
            ".cpio" => "Fichiers CPIO",
            ".csh" => "Fichiers C-Shellscript",
            ".dcr"  => "Fichiers C-Shellscript",
            ".dir"  => "Fichiers -",
            ".dxr" => "Fichiers -",
            ".dvi" => "Fichiers DVI",
            ".evy" => "Fichiers Envoy",
            ".gtar" => "Fichiers archives GNU tar",
            ".hdf" => "Fichiers HDF",
            ".php"   => "Fichiers PHP",
            ".phtml" => "Fichiers PHP",
            ".js" => "Fichiers JavaScript côté serveur",
            ".latex" => "Fichiers source Latex",
            ".bin" => "Fichiers binaires Macintosh",
            ".mif" => "Fichiers FrameMaker Interchange Format",
            ".nc" => "Fichiers Unidata CDF",
            ".cdf" => "Fichiers Unidata CDF",
            ".nsc" => "Fichiers NS Chat",
            ".sh" => "Fichiers Bourne Shellscript",
            ".shar" => "Fichiers atchives Shell",
            ".swf" => "Fichiers Flash Shockwave",
            ".cab" => "Fichiers Flash Shockwave",
            ".spr" => "Fichiers Flash Shockwave",
            ".sprite" => "Fichiers Sprite",
            ".sit" => "Fichiers Stuffit",
            ".sca" => "Fichiers Supercard",
            ".sv4cpio" => "Fichiers CPIO",
            ".sv4crc" => "Fichiers CPIO avec CRC",
            ".tar" => "Fichiers archives tar",
            ".tcl" => "Fichiers script TCL",
            ".tex" => "Fichiers TEX",
            ".texinfo"  => "Fichiers TEXinfo",
            ".texi" => "Fichiers TEXinfo",
            ".t" => "Fichiers TROFF (Unix)",
            ".tr" => "Fichiers TROFF (Unix)",
            ".roff" => "Fichiers TROFF (Unix)",
            ".man"  => "Fichiers TROFF avec macros MAN (Unix)",
            ".troff" => "Fichiers TROFF avec macros MAN (Unix)",
            ".troff" => "Fichiers TROFF avec macros ME (Unix)",
            ".troff" => "Fichiers TROFF avec macros MS (Unix)",
            ".ustar" => "Fichiers archives tar (Posix)",
            ".src" => "Fichiers source WAIS",
            ".zip" => "Fichiers archives ZIP",
            ".au" => "Fichiers JPEG",
            ".snd" => "Fichiers son",
            ".es" => "Fichiers Echospeed",
            ".tsi" => "Fichiers TS-Player",
            ".vox" => "Fichiers Vox",
            ".aif" => "Fichiers JPEG",
            ".aiff" => "Fichiers JPEG",
            ".aifc" => "Fichiers son AIFF",
            ".dus" => "Fichiers JPEG",
            ".cht" => "Fichiers parole",
            ".mid" => "Fichiers JPEG",
            ".midi" => "Fichiers MIDI",
            ".mp2" => "Fichiers MPEG",
            ".ram" => "Fichiers JPEG",
            ".ra" => "Fichiers RealAudio",
            ".rpm" => "Fichiers plugin RealAudio",
            ".stream" => "Fichiers -",
            ".wav" => "Fichiers Wav",
            ".dwf" => "Fichiers Drawing",
            ".cod" => "Fichiers CIS-Cod",
            ".ras" => "Fichiers CMU-Raster",
            ".fif" => "Fichiers FIF",
            ".gif" => "Fichiers GIF",
            ".ief" => "Fichiers IEF",
            ".jpeg" => "Fichiers JPEG",
            ".jpg" => "Fichiers JPEG",
            ".jpe" => "Fichiers JPEG",
            ".tiff" => "Fichiers TIFF",
            ".tif" => "Fichiers TIFF",
            ".mcf" => "Fichiers Vasa",
            ".wbmp" => "Fichiers Bitmap (WAP)",
            ".fh4" => "Fichiers Freehand",
            ".fh5" => "Fichiers Freehand",
            ".fhc" => "Fichiers Freehand",
            ".pnm" => "Fichiers PBM Anymap",
            ".pbm" => "Fichiers Bitmap PBM",
            ".pgm" => "Fichiers PBM Graymap",
            ".ppm" => "Fichiers PBM Pixmap",
            ".rgb" => "Fichiers RGB",
            ".xwd" => "X-Windows Dump",
            ".xbm" => "Fichiers XBM",
            ".xpm" => "Fichiers XPM",
            ".csv" => "Fichiers de données séparées par des virgules",
            ".css" => "Fichiers de feuilles de style CSS",
            ".shtml" => "Fichiers -",
            ".js" => "Fichiers JavaScript",
            ".txt" => "Fichiers pur texte",
            ".rtx" => "Fichiers texte enrichi (Richtext)",
            ".rtf" => "Fichiers Microsoft RTF",
            ".tsv" => "Fichiers de données séparées par des tabulations",
            ".wml" => "Fichiers WML (WAP)",
            ".wmlc" => "Fichiers WMLC (WAP)",
            ".wmls" => "Fichiers script WML (WAP)",
            ".wmlsc" => "Fichiers script C WML (WAP)",
            ".etx" => "Fichiers SeText",
            ".sgm" => "Fichiers MPEG",
            ".sgml" => "Fichiers SGML",
            ".talk" => "Fichiers MPEG",
            ".spc" => "Fichiers Speech",
            ".mpeg" => "Fichiers MPEG",
            ".mpg" => "Fichiers MPEG",
            ".mpe" => "Fichiers MPEG",
            ".qt" => "Fichiers Quicktime",
            ".mov" => "Fichiers Quicktime",
            "viv"  => "Fichiers Vivo",
            ".vivo" => "Fichiers Vivo",
            ".avi" => "Fichiers Microsoft AVI",
            ".movie" => "Fichiers Movie",
            ".vts"  => "3Fichiers DMF",
            ".vtts" => "Fichiers FormulaOne",
            ".3dmf"  => "3Fichiers DMF",
            ".3dm"  => "3Fichiers DMF",
            ".qd3d"  => "3Fichiers DMF",
            ".qd3" => "3Fichiers DMF",
            ".wrl" => "Fichiers VRML",
            "sql" => "Fichier SQL"
        );







        if (in_array($ext, array_keys($e))) {
            return $e[$ext];
        } else {
            if (in_array('.' . $ext, array_keys($e))) {
                $ext = '.' . $ext;
                return $e[$ext];
            } else {

                return $e[''];
            }
        }
    }
}
