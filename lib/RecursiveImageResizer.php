<?php
namespace EKulikova;

require_once 'SingleImageResizer.php';

use EKulikova\SingleImageResizer;
use EKulikova\ImageResizerException;

class RecursiveImageResizer{

    private $originDir;
    private $recursive;

    private $images = array();

    private $function;
    private $arguments = array();

    public function __construct($originDir, $recursive=1){

        $this->originDir = $originDir;
        $this->recursive = $recursive;

    }

    public function __call($function, $arguments){

        $this->function = $function;
        $this->arguments = $arguments;

        $this->loadImages();

        return $this;

    }

    private function loadImages(){

      if( $this->recursive ){
          $this->loadImagesRecursive();
      }
      else{
          $this->loadImagesNotRecursive();
      }
      if( empty( $this->images ) ){
        throw new ImageResizerException( 'There are no images.' );
      }

    }

    private function addImage($fileName){

      if( @exif_imagetype( $fileName ) ){
          $this->images[] = $fileName;
      }

    }

    private function loadImagesRecursive(){

      $directory = new \RecursiveDirectoryIterator( $this->originDir );
      $iterator = new \RecursiveIteratorIterator( $directory );

      foreach ($iterator as $fileInfo) {

        if( $fileInfo->isFile() ) {
          $fileName = $fileInfo->getPathname();
          $this->addImage($fileName);
        }

      }

    }

    private function loadImagesNotRecursive(){

      $directory = new \DirectoryIterator($this->originDir);

      foreach($directory as $file){

          if ( !$file->isDot()  && !$file->isDir() ){

            $fileName = $file->getPathname();
            $this->addImage($fileName);

          }

      }

    }

    private function getNewPath($pathName, $newDir){

      if ($newDir) {
        return str_replace($this->originDir, $newDir, $pathName);
      } else {
        return null;
      }

    }

    public function save( $newDir=null ){

      foreach ($this->images as $pathName) {

              try{

                $newPath = $this -> getNewPath($pathName, $newDir);

                $img = new SingleImageResizer($pathName);
                call_user_func_array( array($img, $this->function), $this->arguments )
                    ->save($newPath);

              } catch ( ImageResizerException $e ) {

                echo $e->getMessage()."\n";

              }

      }
    }

}
