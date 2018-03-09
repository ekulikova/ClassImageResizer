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

        if( $fileInfo->getType() == 'file'  ) {

          echo $fileInfo->getPathname() . "\n";
          $this->images[] = $fileInfo->getFilename();

        }

      }

        return $this->images;

    }

/*    public function resize($new_width, $new_height, $recursive=1){

        $images = $this->getImages($recursive);

        // foreach and resize

        retrurn count($images);

    }
*/
}

class RecursiveImageResizerException extends \Exception
{
}
