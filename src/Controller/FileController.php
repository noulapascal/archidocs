<?php

namespace App\Controller;

use App\Entity\File;
use App\Form\FileType;
use App\Form\UploadType;
use App\Entity\Directory;
use App\Repository\FileRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route(
 *     "/{_locale}/file",
 *     requirements={
 *         "_locale": "en|fr|de",
 *     }
 * ) 
 * @IsGranted("IS_AUTHENTICATED_FULLY") 
 */
class FileController extends AbstractController
{
    /**
     * @Route("/", name="file_index", methods={"GET"})
     */
    public function index(FileRepository $fileRepository): Response
    {

        $currentUser = $this->getUser();
        if (!empty($currentUser)) {


            $company = $currentUser->getDivision()->getCompany();
            $files = $fileRepository->findByCompanyDivisionWithNoParent($currentUser->getDivision());
        }

        return $this->render('file/index.html.twig', [
            'files' => $files,
        ]);
        return $this->render('file/index.html.twig', [
            'files' => $fileRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="file_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $file = new File();
        $form = $this->createForm(FileType::class, $file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $file->setPath();
            $entityManager->persist($file);
            $entityManager->flush();

            return $this->redirectToRoute('file_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('file/new.html.twig', [
            'file' => $file,
            'form' => $form->createView(),
        ]);
    }





    /**
     * @Route("/folder/{id}/new", name="file_folder_new", methods={"GET","POST"})
     */
    public function newFile(Request $request, Directory $directory): Response
    {
        $file = new File();
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
        
            $this->upload($directory,$form['files']->getData());

            

            return $this->redirectToRoute('file_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('file/new.html.twig', [
            'file' => $file,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="file_show", methods={"GET"})
     */
    public function show(File $file): Response
    {
        return $this->render('file/show.html.twig', [
            'file' => $file,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="file_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, File $file): Response
    {
        $form = $this->createForm(FileType::class, $file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('file_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('file/edit.html.twig', [
            'file' => $file,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="file_delete", methods={"POST"})
     */
    public function delete(Request $request, File $file): Response
    {
        if ($this->isCsrfTokenValid('delete' . $file->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($file);
            $entityManager->flush();
        }

        return $this->redirectToRoute('file_index', [], Response::HTTP_SEE_OTHER);
    }









    public function FolderTruePath($file)
    {
        $name = $file->getName();
        $path = $file->getPath();
        $parent = $file->getParent();
        $companyCode =  $file->getCompanyDivision()[0]->getCompany()->getCode();
        $dirName = './' . $_ENV['DATA_DIR'] . $companyCode . '/' . $file->getPath();
        /*
        if (is_dir($dirName)) {
            $parent = $file->getParent();
            if (!empty($parent)) {
                $newPath =  './' . $_ENV['DATA_DIR'] . $companyCode .
                    $parent->getPath() . '/' . $newName;
                //$dirPath = $parent->getPath() . '/' . $newName;
                // $file->setPath($dirPath);
            } else {
                $newPath =  './' . $_ENV['DATA_DIR'] . $companyCode . '/' . $newName;
            }
            rename($dirName, $newPath);

            //    rename($dirName, $newPath);

        } else {
            echo "ce dossier n'existe déja plus";
            //mkdir($dirName);
            //$this->dirRenamer($file,$newName);
        }

        */




        //   if (is_dir($dirName)) {
        // $dir = opendir($dirName);
        //$dirName = $path;
        // } else
        if (!is_dir($dirName)) {


            if (!empty($parent)) {

                $path = $_ENV['DATA_DIR'] . $parent->getCompanyDivision()[0]->getCompany()->getCode();
                $parent = $file->getParent();
                while ($parent) {
                    $names[] = '/' . $parent->getName();
                    $parent = $parent->getParent();
                }
                $names_reverse = array_reverse($names);
                foreach ($names_reverse as $key => $name) {
                    # code...
                    $path .= '/' . $name;
                }
                $path .= '/' . $file->getName();
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



    public function addScheme2($item, File $file,Directory $directory)
    {

        $em = $this->getDoctrine()->getManager();

        $namedir = $_ENV['DATA_DIR'] . $directory->getCompanyDivision()[0]->getCompany()->getCode() . '/' . $directory->getPath();
        $entry = $item->getClientOriginalName();
        //var_dump($item->getClientOriginalName());

        $tab['name'] = $entry;
        $tab['type'] = filetype($namedir . "/" . $entry);
        $tab['date'] = filemtime($namedir . "/" . $entry);
        $tab['size'] = filesize($namedir . "/" . $entry);
        $tab['path'] = $namedir . "/" . $entry;
        
        $tab['parentFolder'] = $directory;
        
        $t = explode(".", $entry);
        $tab['extension'] = $t[count($t) - 1];
        $tab['fileType'] = $this->assocExt($tab['extension']);
        foreach ($tab as $key => $value) {
            $method = 'set'.ucfirst($key);
            if (method_exists($file, $method)) {
                //var_dump('ok');
                $file->$method($value);
                # code...
            }
            # code...
        }

        if($file->getName()){
            $file->setAuthor($this->getUser());
            $file->setParentFolder($directory);
            $em->persist($file);
            $em->flush();

    

        }
        return $file;
    }



    public function upload(Directory $directory, array $files)
    {

        $namedir = $_ENV['DATA_DIR'] . $directory->getCompanyDivision()[0]->getCompany()->getCode() . '/' . $directory->getPath();

        if (is_dir($namedir)) {
            $dir = opendir($namedir);
        } else {

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
        foreach ($files as $key => $item) {
            $path = './' . $directory->getCompanyDivision()[0]->getCompany()->getCode() . '/' . $directory->getPath() . '/';
            $name = $item->getClientOriginalName();
            $item->move($namedir, $name);
            $file = new File();
            $this->addScheme2($item,$file,$directory);
        }
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
