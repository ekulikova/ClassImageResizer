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

    public function getImages($recursive){

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

        return $this->images;

    }

    public function resize($new_width, $new_height, $recursive=1){

        $images = $this->getImages($recursive);

        foreach ($images as $image) {

            $img = new imageResizer($image);
            $img -> resize($new_width, $new_height);
            $img -> save();

        }

    }

}

class RecursiveImageResizerException extends \Exception
{
}
