<?php
namespace EKulikova;

require_once 'ImageResizer.php';

use EKulikova\ImageResizer;


class RecursiveImageResizer{

    private $dir;
    private $images;

    public function __construct($dir){
        $this->dir = $dir;
    }

    private function getImagesRecursive(){

      $directory = new \RecursiveDirectoryIterator( $this->dir );
      $iterator = new \RecursiveIteratorIterator( $directory );

      foreach ($iterator as $fileInfo) {

        if( $fileInfo->isFile() ) {

          $fileName = $fileInfo->getPathname();



          if( @exif_imagetype( $fileName ) ){

              $this->images[] = $fileName;

          }

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

            if( @exif_imagetype( $fileName ) ){

                $this->images[] = $fileName;

            }

          }
      }

    }

    public function getImages($recursive){

      if( $recursive ){
          $this->getImagesRecursive();
      }
      else{
          $this->getImagesNotRecursive();
      }

        return $this->images;

    }

    public function resize($new_width, $new_height, $recursive=1){

        $this->getImages($recursive);

        foreach ($this->images as $image) {

            $img = new imageResizer($image);
            $img -> resize($new_width, $new_height);
            $img -> save();

        }
    }

    public function resizeToHeight($new_height, $recursive=1, $skip_small=1){

        $this->getImages($recursive);

        foreach ($this->images as $image) {

            $img = new imageResizer($image);
            $img -> resizeToHeight($new_height, $skip_small);
            $img -> save();

        }
    }

    public function resizeToWidth($new_width, $recursive=1, $skip_small=1){

        $this->getImages($recursive);

        foreach ($this->images as $image) {

            $img = new imageResizer($image);
            $img -> resizeToWidth($new_width, $skip_small);
            $img -> save();

        }
    }

    public function resizeToHeightWidth($new_width, $new_height, $recursive=1, $skip_small=1){

        $this->getImages($recursive);

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
