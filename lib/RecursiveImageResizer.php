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

        return $this;

    }

    public function save( $newDir=null ){

      $iterator = new \RecursiveIteratorIterator(
          new \RecursiveDirectoryIterator( $this->originDir ) );

      foreach ($iterator as $pathName => $fileInfo) {

            if( $fileInfo->getType() == 'file'  ) {
              try{

                $img = new SingleImageResizer($pathName);
                call_user_func_array( array($img, $this->function), $this->arguments )
                    ->save();

              } catch ( ImageResizerException $e ) {

                echo $e->getMessage()."\n";

              }

            }

      }
    }

}
