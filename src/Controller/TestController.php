<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function index()
    {
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    /**
     * @Route("/testdir/{file?}", name="testdir")
     */
    public function showDir ($file){

        $this->list_dir('./','.',1);
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',

        ]);
}

public function list_dir($base, $cur, $level=0) {
        global $PHP_SELF, $BASE, $order, $asc;
        if ($dir = opendir($base)) {
        $tab = array();
        while($entry = readdir($dir)) {
        if(is_dir($base."/".$entry) && !in_array($entry, array(".",".."))) {
        $tab[] = addScheme($entry, $base, 'dir');
        }
    }

}

}
}