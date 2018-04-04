<?php
namespace EKulikova;

require_once 'ImageResizer.php';
require_once 'IResizer.php';

use EKulikova\ImageResizer;


class RecursiveImageResizer implements iResizer{

    private $dir;
    private $recursive;
    private $images;

    public function __construct($dir, $recursive){
        $this->dir = $dir;

        $this->recursive = $recursive;
    }

    private function addImage($fileName){

      if( @exif_imagetype( $fileName ) ){

          $this->images[] = $fileName;

      }

    }

    private function getImagesRecursive(){

      $directory = new \RecursiveDirectoryIterator( $this->dir );
      $iterator = new \RecursiveIteratorIterator( $directory );

      foreach ($iterator as $fileInfo) {

        if( $fileInfo->isFile() ) {

          $fileName = $fileInfo->getPathname();
          $this->addImage($fileName);

        }

      }

    }

    private function getImagesNotRecursive(){

      $directory = new \DirectoryIterator($this->dir);

      foreach($directory as $file)
      {
          if ( !$file->isDot()  && !$file->isDir() )
          {

            $fileName = $file->getPathname();
            $this->addImage($fileName);

          }
      }

    }

    public function getImages(){

      if( $this->recursive ){
          $this->getImagesRecursive();
      }
      else{
          $this->getImagesNotRecursive();
      }

        return $this->images;

    }

    public function resize($new_width, $new_height){

        $this->getImages();

        foreach ($this->images as $image) {

            $img = new imageResizer($image);
            $img -> resize($new_width, $new_height);
            $img -> save();

        }
    }

    public function resizeToHeight($new_height, $skip_small=1){

        $this->getImages();

        foreach ($this->images as $image) {

            $img = new imageResizer($image);
            $img -> resizeToHeight($new_height, $skip_small);
            $img -> save();

        }
    }

    public function resizeToWidth($new_width, $skip_small=1){

        $this->getImages();

        foreach ($this->images as $image) {

            $img = new imageResizer($image);
            $img -> resizeToWidth($new_width, $skip_small);
            $img -> save();

        }
    }

    public function resizeToHeightWidth($new_width, $new_height, $skip_small=1){

        $this->getImages();

        foreach ($this->images as $image) {

            $img = new imageResizer($image);
            $img -> resizeToHeightWidth($new_width, $new_height, $skip_small);
            $img -> save();

        }
    }

}

class RecursiveImageResizerException extends \Exception
{
}
