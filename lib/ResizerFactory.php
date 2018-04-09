<?php
namespace EKulikova;

require_once 'ImageResizer.php';
require_once 'RecursiveImageResizer.php';

use EKulikova\ImageResizer;
use EKulikova\RecursiveImageResizer;

class ResizerFactory{

    public static function getResizer($file, $recursive=1){

        if( is_dir($file) ){
           return new RecursiveImageResizer($file, $recursive);
        }
        elseif( is_file($file) ){
          return new ImageResizer($file);
        }
        else{
          return null;
        }

    }

}
