<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Region;
use App\Entity\Division;
use App\Entity\Subdivision;
use App\Form\SubUploadType;
use App\Entity\Municipality;
use App\Form\SubdivisionType;
use App\Repository\SubdivisionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route(
 *     "/{_locale}/subdivision",
 *     requirements={
 *         "_locale": "en|fr|de",
 *     }
 * ) 
 * @IsGranted("IS_AUTHENTICATED_FULLY") 
 */
class SubdivisionController extends AbstractController
{
    /**
     * @Route("/", name="subdivision_index", methods={"GET"})
     */
    public function index(SubdivisionRepository $subdivisionRepository): Response
    {
        return $this->render('subdivision/index.html.twig', [
            'subdivisions' => $subdivisionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="subdivision_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $subdivision = new Subdivision();
        $form = $this->createForm(SubdivisionType::class, $subdivision);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($subdivision);
            $entityManager->flush();

            return $this->redirectToRoute('subdivision_index');
        }

        return $this->render('subdivision/new.html.twig', [
            'subdivision' => $subdivision,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="subdivision_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Subdivision $subdivision): Response
    {
        $form = $this->createForm(SubdivisionType::class, $subdivision);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('subdivision_index');
        }

        return $this->render('subdivision/edit.html.twig', [
            'subdivision' => $subdivision,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="subdivision_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Subdivision $subdivision): Response
    {
        if ($this->isCsrfTokenValid('delete'.$subdivision->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($subdivision);
            $entityManager->flush();
        }

        return $this->redirectToRoute('subdivision_index');
    }
    
    /**
     * @Route("/upload_subdivision",name="upload_sub")
     * @param Request $request
     * @return type
     */
    
    public function upload(Request $request) {
        $form = $this->createForm(SubUploadType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $dir= __DIR__."/../../public/uploads/media/";
        $media1 = $form['file']->getData();
        $name= $media1->getClientOriginalName();
        $media1->move($dir,$name);
        
            
        dump($media1);
        var_dump($media1);
        }
        if(isset($media1) && !empty($name)){
          //  require_once __DIR__.'../../vendor/autoload.php';
          //  require_once __DIR__.'/../../../web/uploads/media/'.''.$media1->getFilename();
            if (($handle = fopen($dir.''.$name, "r")) !== FALSE) {
                $i = 0;
                
                        $em1 = $this->getDoctrine()->getManager();
                        $em2 = $this->getDoctrine()->getManager();
                        $em3 = $this->getDoctrine()->getManager();
                        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                    
                            $num = count($data);
                    //        echo "<p> $num champs à la ligne $i: <br /></p>\n";
                    /*
                    echo $data[$c] . "<br />\n";
                    }
                    */
                    if($i==0){
                        $enTetes = $data;
                    }
                    if($i>0)
                    {
                        $subdivision = new Subdivision();
                        $j = 0;
                        
                       $donnees = array_combine($enTetes, $data);
                    //   var_dump($donnees);
                        foreach ($enTetes as $key => $value) {
                                
                                        
                                        $city = $this->getDoctrine()->getManager()->getRepository("App:City")->findOneBy(array( 'name' => $donnees['city'],
                                            ));
                                        if (!empty($city))
                                        {
                                            $subdivision->setCity($city);
                                       
                                  }
                                  
                                      
                                      
                                     else{
                                          $division = $this->getDoctrine()->getManager()->getRepository('App:Division')->findOneBy([
                                            'name'=> strval(utf8_decode($donnees['division']))
                                        ]);  

                                      if (!empty($division)){
                                          $method = 'set'.ucfirst('Division');
                                    if(method_exists( $city , $method))
                                    {
                                        $city->$method($division);
                                    }
                                      }
                                       else {
                                          $region = $this->getDoctrine()->getManager()->getRepository('App:Region')->findOneBy([
                                            'name'=> strval(utf8_decode($donnees['region']))
                                        ]);  
                                          if(empty($region)){
                                              $region=new \App\Entity\Region();
                                              $region->setName(strval(utf8_decode($donnees['region'])));
                                              $country = $em1->getRepository(\App\Entity\Country::class)->findOneBy([
                                                  'name'=>'Cameroun'
                                              ]);
                                              $region->setCountry($country);
                                              $em1->persist($region);
                                              $em1->flush();
                                          }
                                          
                                          $division = new Division();
                                          $division->setName($donnees['division']);
                                          $division->setRegion($region);

                                    $method = 'set'.ucfirst('division');
                                    if(method_exists($city, $method))
                                    {
                                        $city->$method($division);
                                    }
                                  }

                                  $city = new City();
                                  if (!empty($donnees['city']))
                                  {
                                      $city->setName(strval(utf8_decode($donnees['city'])));
                                  }
                                  else{
                                  $city->setName(strval(utf8_decode($donnees['name'])));                                      
                                  }
                                  $city->setDivision($division);
                                  $em1->persist($city);
                                  $em1->flush();    
                                  
                                         
                                      }
                                           foreach ($donnees as $cle => $val) {
                                            $method = 'set'.ucfirst($cle);
                                            $method2 = 'get'.ucfirst($cle);
                                            if(method_exists($subdivision , $method) and empty($subdivision->$method2()))
                                    {
                                                if($cle == 'city'){
                                                   $subdivision->$method($city); 
                                                }
                                                else{
                                                    $subdivision->$method(strval(utf8_decode($val)));
                                                }
                                        
                                    }  
                                        }

                                      
                                      
                                  
                                  
                                            
                                    
                                    
                                  
                                    
                                
                          
                                
                                 
                            
                            $j++;
                            }
                            
                            
               if(!(empty($subdivision->getName())))
               {
                   
                   $sub = $this->getDoctrine()->getRepository(Subdivision::class)->findOneBy([
                       'name'=>$subdivision->getName()]);
                   if(empty($sub)){
                       
                   $em1->persist($subdivision); 
                   $em1->flush();  
                   }
                   /*
                   $mun = $this->getDoctrine()->getRepository(Municipality::class)->findOneBy([
                       'name'=>$subdivision->getName()]);
                   if(empty($mun)){
                   $municipality = new Municipality();
                   $municipality->setName($subdivision->getName());
                   $municipality->setSubdivision($subdivision);
                   $municipality->setLocation($subdivision->getName());
                   $em1->persist($municipality);
                   $em1->flush();
                   }
                   */
                    
               }
                   
                   
               }else 
               {
                   $em1->flush();
               }
;
                        
                     
                     
                                                 
        $i++;  }
            
         fclose($handle);
                
                }
                
                
                // $em->remove($media1);
           }

  
   
    return $this->render('subdivision/new.html.twig',[
        'form'=>$form->createView()
    ]);
    }
    
    
    
    /**
     * @Route("/{id}", name="subdivision_show", methods={"GET"})
     */
    public function show(Subdivision $subdivision): Response
    {
        return $this->render('subdivision/show.html.twig', [
            'subdivision' => $subdivision,
        ]);
    }

    
}
