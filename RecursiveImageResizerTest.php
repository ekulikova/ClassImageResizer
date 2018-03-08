<?php

require_once 'RecursiveImageResizer.php';

use EKulikova\RecursiveImageResizer;
use EKulikova\RecursiveImageResizerException;


if (version_compare(PHP_VERSION, '7.0.0') >= 0 && !class_exists('PHPUnit_Framework_TestCase')) {
	class_alias('PHPUnit\Framework\TestCase', 'PHPUnit_Framework_TestCase');
}

class RecursiveImageResizerTest extends PHPUnit_Framework_TestCase
{

  /**
	 * Helpers
	 */

   private function getDirName($currentDepth){

      $tmp_dir = sys_get_temp_dir();

      for($i = 1; $i<=$currentDepth; $i++){
        $tmp_dir .= '/RecursiveImageResizer'.$i;
      }

      return $tmp_dir;
   }

   private function createStructure($depth, $quantity){

      for($i = 1; $i <= $depth; $i++){
        // create dir
        //mkdir($this->getDirName());
        echo "{$this->getDirName($i)}\n";
        // cd to it

      }

   }

   /**
   * Tests
   */

   public function testGetImages(){

      $dir = $this->createStructure(3, 2);

      /*$rec = new RecursiveImageResizer($dir);

      $images = $rec->getImages(0);

      $this->assertEquals( count($images) ,6 );
*/
   }

  /**
  * Resize tests
  */
/*
  public function testResize(){

      //create structure
      $dir = $this->createStructure();

      $rec = new RecursiveImageResizer($dir);

      $rec->resize(100, 100, 0);

      // check result

      //destroy structure

  }
*/
}
