<?php
namespace EKulikova;

require_once 'SingleImageResizer.php';

use EKulikova\SingleImageResizer;
use EKulikova\ImageResizerException;

class RecursiveImageResizer{

    private $originDir;
    private $recursive;

    public function __construct($originDir, $recursive=1){

        $this->originDir = $originDir;

        $this->recursive = $recursive;
    }

    public function __call($function, $arguments){

      $iterator = new \RecursiveIteratorIterator(
          new \RecursiveDirectoryIterator( $this->originDir ) );

      foreach ($iterator as $pathName => $fileInfo) {

            if( $fileInfo->getType() == 'file'  ) {
              try{

                $img = new SingleImageResizer($pathName);
                call_user_func_array( array($img, $function), $arguments )->save();

              } catch ( ImageResizerException $e ) {

                echo $e->getMessage()."\n";

              }

            }

      }
    }

}
