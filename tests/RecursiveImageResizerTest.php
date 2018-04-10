<?php

require_once __DIR__.'/../lib/RecursiveImageResizer.php';

use EKulikova\RecursiveImageResizer;
use EKulikova\RecursiveImageResizerException;


if (version_compare(PHP_VERSION, '7.0.0') >= 0 && !class_exists('PHPUnit_Framework_TestCase')) {
	class_alias('PHPUnit\Framework\TestCase', 'PHPUnit_Framework_TestCase');
}

class RecursiveImageResizerTest extends PHPUnit_Framework_TestCase
{

	private $structure = array(
		'dirs' => [],
		'files' => [],
	);

	private $orig_width = 500;
	private $orig_height = 500;
	private $img_type = 'jpeg';
	private $testDir;

	protected function setUp(){
			$this->createStructure(3, 2);
	}

	protected function tearDown(){
			$this->destroyStructure();
	}

  /**
	 * Helpers
	 */

   private function getDirName($currentDepth){

      $tmp_dir = sys_get_temp_dir().'/'.$this->testDir;

      for($i = 1; $i<=$currentDepth; $i++){
        $tmp_dir .= '/RecursiveImageResizer'.$i;
      }

      return $tmp_dir;
   }

	 private function getTempFile($dirName)
	 {
		 $tmp_file = tempnam($dirName, 'RecursiveImageResizerTest');

		 return $tmp_file;
	 }

	 private function createImage($dirName){

		 $image = imagecreatetruecolor($this->orig_width, $this->orig_height);

		 $filename = $this->getTempFile($dirName);

		 $output_function = 'image' . $this->img_type;

		 if( $output_function($image, $filename)){

			 $this->structure['files'][] = $filename;

			 return $filename;

		 }

		 return false;

	 }

	 private function createImages($dirName, $quantity){

		 for( $i=1; $i<=$quantity; $i++ ){

			 	$this->createImage($dirName);

		 }

	 }

	 private function createDir($dirName){

		 if( mkdir($dirName) ){

				 $this->structure['dirs'][] = $dirName;

				 return $dirName;

		 }

		 return false;

	 }

	 private function createTxtFile( $dirName ){

		 	$fileName = $this->getTempFile( $dirName );
			file_put_contents( $fileName,'text file' );
			$this->structure['files'][] = $fileName;

			return $fileName;

	 }

   private function createStructure($depth, $quantity){

      for($i = 1; $i <= $depth; $i++){

				$dirName = $this->getDirName($i);

				if ( $this->createDir( $dirName ) ){

					$this->createImages( $dirName, $quantity );
					$this->createTxtFile( $dirName );

				}

      }

			$this->testDir = $this->structure['dirs'][0];

   }

	 private function destroyStructure(){

		 	foreach ( $this->structure['files'] as $file ) {

				unlink($file);

			}

			$this->structure['files'] = [];

			foreach( array_reverse( $this->structure['dirs'] ) as $dir ){

					rmdir($dir);

			}

			$this->structure['dirs'] = [];

	 }

   /**
   * Tests
   */

	 /**
     * @dataProvider getImagesProvider
     */

   public function testGetImages($recursive, $expected){

      $rec = new RecursiveImageResizer( $this->testDir, $recursive );

      $images = $rec->getImages();
			
      $this->assertEquals( count($images) ,$expected );

   }

	 public function getImagesProvider()
    {
        return [
            [1, 6],
            [0, 2],
        ];
    }

  /**
  * Resize tests
  */

  public function testResize(){

      $rec = new RecursiveImageResizer($this -> testDir, 1);

      $rec->resize(100, 100);

			$images = $rec->getImages();

			foreach ( $images as $img ) {

				list( $width, $height ) = getimagesize( $img );
				$this->assertEquals(100, $width);
				$this->assertEquals(100, $height);

			}
  }

	public function testResizeToHeight(){

      $rec = new RecursiveImageResizer($this -> testDir, 1);

      $rec->resizeToHeight(100, 1);

			$images = $rec->getImages();

			foreach ( $images as $img ) {

				list( $width, $height ) = getimagesize( $img );
				$this->assertEquals(100, $height);

			}
  }

	public function testResizeToWidth(){

      $rec = new RecursiveImageResizer($this -> testDir, 1);

      $rec->resizeToWidth(100, 1);

			$images = $rec->getImages();

			foreach ( $images as $img ) {

				list( $width, $height ) = getimagesize( $img );
				$this->assertEquals(100, $width);

			}
  }

	public function testResizeToHeightWidth(){

      $rec = new RecursiveImageResizer($this -> testDir, 1);

      $rec->resizeToHeightWidth(100, 100, 1);

			$images = $rec->getImages();

			foreach ( $images as $img ) {

				list( $width, $height ) = getimagesize( $img );
				$this->assertEquals(100, $width);
				$this->assertEquals(100, $height);

			}
  }

}
