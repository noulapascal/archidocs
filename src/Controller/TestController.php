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

        $tab[] = addScheme($entry, $base, 'dir');

        $this->list_dir('./','.',1);
        return $this->render('directory/index.html.twig', [
            'directory' => '',

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



function addScheme($entry,$base,$type) {
    $tab['name'] = $entry;
    $tab['type'] = filetype($base."/".$entry);
    $tab['date'] = filemtime($base."/".$entry);
    $tab['size'] = filesize($base."/".$entry);
    $tab['perms'] = fileperms($base."/".$entry);
    $tab['access'] = fileatime($base."/".$entry);
    $t = explode(".", $entry);
    $tab['ext'] = $t[count($t)-1];
    return $tab;
  }




  public function list_file($cur)
{
  global $PHP_SELF, $order, $asc, $order0, $usr, $tb, $srh, $chmp, $BASE;
  if ($dir = opendir($cur)) {
    /* tableaux */
    $tab_dir = array();
    $tab_file = array();
    /* extraction */
    while ($file = readdir($dir)) {
      if (is_dir($cur . "/" . $file)) {
        if (!in_array($file, array(".", ".."))) {
          $tab_dir[] = addScheme($file, $cur, 'dir');
        }
      } else {
        $tab_file[] = addScheme($file, $cur, 'file');
      }
    }
    /* tri */
    if (isset($_GET['order'])) {
      $order = $_GET['order'];
      usort($tab_dir, "cmp_$order");
      usort($tab_file, "cmp_$order");
    }

    /* affichage */


    if ($cur != $BASE) {
      foreach ($tab_dir as $elem) {
        // $t=slt($tb,$srh,$elem['name'],$chmp);
        // global $usr;
        // if(is_array($t)){
        // $a=implode($t,' ');
}
    } else {
      foreach ($tab_dir as $elem) {
        $t = slt($tb, $srh, $elem['name'], $chmp);
        global $usr;
        if (is_array($t)) {
          $a = implode($t, ' ');
          if (strpos($a, $usr)) {

          }
        }
      }
    }

    echo "</table>";
    closedir($dir);
  } else {
    if (!is_dir($cur)) {
      mkdir($cur);
      list_file($cur);
    }
  }
}
}