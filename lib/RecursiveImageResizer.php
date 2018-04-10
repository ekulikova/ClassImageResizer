<?php
namespace EKulikova;

require_once 'ImageResizer.php';
require_once 'IResizer.php';

use EKulikova\ImageResizer;


class RecursiveImageResizer implements iResizer{

    private $originDir;
    private $recursive;
    private $images;

    public function __construct($originDir, $recursive=1){

        $this->originDir = $originDir;
        $this->recursive = $recursive;

        $this->images = $this->setImages();
    }

    private function addImage($fileName){

      if( @exif_imagetype( $fileName ) ){

          $this->images[] = $fileName;

      }

    }

    private function setImagesRecursive(){

      $directory = new \RecursiveDirectoryIterator( $this->originDir );
      $iterator = new \RecursiveIteratorIterator( $directory );

      foreach ($iterator as $fileInfo) {

        if( $fileInfo->isFile() ) {

          $fileName = $fileInfo->getPathname();
          $this->addImage($fileName);

        }

      }

    }

    private function setImagesNotRecursive(){

      $directory = new \DirectoryIterator($this->originDir);

      foreach($directory as $file)
      {
          if ( !$file->isDot()  && !$file->isDir() )
          {

            $fileName = $file->getPathname();
            $this->addImage($fileName);

          }
      }

    }

    private function setImages(){

      if( $this->recursive ){
          $this->setImagesRecursive();
      }
      else{
          $this->setImagesNotRecursive();
      }

        return $this->images;

    }

    public function getImages(){

      return $this->images;

    }

    public function resize($new_width, $new_height){

        foreach ($this->images as $image) {

            $img = new imageResizer($image);
            $img -> resize($new_width, $new_height)-> save();

        }
    }

    public function resizeToHeight($new_height, $skip_small=1){

        foreach ($this->images as $image) {

            $img = new imageResizer($image);
            $img -> resizeToHeight($new_height, $skip_small) -> save();

        }
    }

    public function resizeToWidth($new_width, $skip_small=1){

        foreach ($this->images as $image) {

            $img = new imageResizer($image);
            $img -> resizeToWidth($new_width, $skip_small)->save();

        }
    }

    public function resizeToHeightWidth($new_width, $new_height, $skip_small=1){

        foreach ($this->images as $image) {

            $img = new imageResizer($image);
            $img -> resizeToHeightWidth($new_width, $new_height, $skip_small) -> save();

        }
    }

}

class RecursiveImageResizerException extends \Exception
{
}
