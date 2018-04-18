<?php
namespace EKulikova;

require_once 'SingleImageResizer.php';
require_once 'RecursiveImageResizer.php';

use EKulikova\SingleImageResizer;
use EKulikova\ImageResizerException;
use EKulikova\RecursiveImageResizer;

class ImageResizer{

    public static function getResizer($file, $recursive=1){

        if( is_dir($file) ){
           return new RecursiveImageResizer($file, $recursive);
        }
        elseif( is_file($file) ){
          return new SingleImageResizer($file);
        }
        else{
          throw new ImageResizerException($file.' does not exist.');
        }

    }

}
