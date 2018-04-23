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

     public function __call($function, $arguments) {

       $items = scandir( $this->originalDir, SCANDIR_SORT_NONE );

       foreach( $items as $item ){

          if( is_dir($item) ){
            $this->__call($func, $arguments);
          }
          elseif( @exif_imagetype($item) ){

            $img = new SingleImageResizer($item);
            call_user_func_array( array($img, $function), $arguments );

          }
          else{
            throw new ImageResizerException($item.' does not an image.');
          }

       }

     }

    /*private function addImage($fileName){

      if( @exif_imagetype( $fileName ) ){

          $this->images[] = $fileName;

      }

    }*/

  /*  public function resize($new_width, $new_height){

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
*/
}
