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

        $this->setDir( $originDir );

        $this->recursive = $recursive;

        $this->setImages();
    }

    private function setDir( $originDir ){

      if( is_Dir($originDir) ){
        $this->originDir = $originDir;
      }
      else{
        throw new RecursiveImageResizerException( 'Directory '.$originDir.' does not exists.' );
      }

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

      if( empty( $this->images ) ){
        throw new RecursiveImageResizerException( 'There is no images.' );
      }

      return $this->images;

    }

    public function getImages(){

      return $this->images;

    }

    public function resize($new_width, $new_height){

        foreach ($this->images as $image) {

          try{

            $img = new imageResizer($image);
            $img -> resize($new_width, $new_height)-> save();

          } catch ( ImageResizerException $e ) {

            echo "Error: ".$e->getMessage();

          }

        }
    }

    public function resizeToHeight($new_height, $skip_small=1){

        foreach ($this->images as $image) {
          try{

            $img = new imageResizer($image);
            $img -> resizeToHeight($new_height, $skip_small) -> save();

          } catch ( ImageResizerException $e ) {

            echo "Error: ".$e->getMessage();

          }
        }
    }

    public function resizeToWidth($new_width, $skip_small=1){

        foreach ($this->images as $image) {
          try{

            $img = new imageResizer($image);
            $img -> resizeToWidth($new_width, $skip_small)->save();

          } catch ( ImageResizerException $e ) {

            echo "Error: ".$e->getMessage();

          }
        }
    }

    public function resizeToHeightWidth($new_width, $new_height, $skip_small=1){

        foreach ($this->images as $image) {
          try{

            $img = new imageResizer($image);
            $img -> resizeToHeightWidth($new_width, $new_height, $skip_small) -> save();

          } catch ( ImageResizerException $e ) {

            echo "Error: ".$e->getMessage();

          }
        }
    }

}

class RecursiveImageResizerException extends \Exception
{
}
