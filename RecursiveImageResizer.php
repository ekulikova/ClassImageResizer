<?php
namespace EKulikova;

require_once 'ImageResizer.php';

use EKulikova\ImageResizer;
use \DirectoryIterator;


class RecursiveImageResizer{

    private $dir;
    private $images;

    public function __construct($dir){
        $this->dir = $dir;
    }

    public function getImages($recursive){

        foreach ( new DirectoryIterator( $this->dir ) as $fileInfo ) {

          if( $fileInfo->isDir() || $fileInfo->isDot()  ) continue;

          echo $fileInfo->getFilename() . "\n";
          $this->images[] = $fileInfo->getFilename();

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
