<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// src/Form/DataTransformer/StringToFileTransformer.php
namespace App\Form\DataTransformer;

use Symfony\Component\HttpFoundation\File\File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class StringToFileTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms an object (file) to a string (number).
     *
     * @param  File|null $file
     */
    public function reverseTransform($file): string
    {
        if (empty($file)) {
            return '';
        }

        return $file->getFilename();
    }

    /**
     * Transforms a string (number) to an object (file).
     *
     * @param  string $name
     * @throws TransformationFailedException if object (file) is not found.
     */
    public function transform($name): ?File
    {
        // no file number? It's optional, so that's ok
        if (!$name) {
            return null;
        }
       // $entier = intval(str_ireplace('product_','', $name));
         $path = __DIR__.'/../../../public/logo_and_banner/'.$name;
        $file = new File($path);
        if (null === $file) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'A file with string "%s" does not exist!',
                $name
            ));
        }

        return $file;
    }
}
