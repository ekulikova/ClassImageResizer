<?php

require_once __DIR__.'/../lib/ImageResizer.php';
require_once __DIR__.'/../lib/SingleImageResizer.php';

use EKulikova\ImageResizer;
use EKulikova\ImageResizerException;
use EKulikova\SingleImageResizer;


if (version_compare(PHP_VERSION, '7.0.0') >= 0 && !class_exists('PHPUnit_Framework_TestCase')) {
	class_alias('PHPUnit\Framework\TestCase', 'PHPUnit_Framework_TestCase');
}

class ImageResizerTestNew extends PHPUnit_Framework_TestCase
{

  private $tmp_files = array();
	private $tmp_dirs = array();

	protected function tearDown(){
			$this->destroyStructure();
	}

  /**
	 * Helpers
	 */

	 private function createImage($width, $height, $type, $filename=null)
	 {

		 $filename || $filename = $this->getTempFile();

		 $image = imagecreatetruecolor($width, $height);

		 $output_function = 'image' . $type;
		 $output_function($image, $filename);

		 array_push($this->tmp_files, $filename);

		 return $filename;
	 }

   private function getTempFile(){

		 return tempnam(sys_get_temp_dir(), 'ImageResizerTest');

	 }

	 private function getTempDir(){

		 return sys_get_temp_dir().'/ImageResizerTest';

	 }

	 private function createDir($dirName){

		 if( mkdir($dirName) ){

				 array_push($this->tmp_dirs, $dirName);

				 return $dirName;

		 }

		 return false;

	 }

	 private function createTxtFile( $fileName ){

		 	file_put_contents( $fileName,'text file' );
			array_push($this->tmp_files,$fileName);

			return $fileName;

	 }

	 private function createDirectoryStructure($depth, $imgQuantity, $txtQuantity){

		 $tempDir = $this->getTempDir();

		 $dir = $this->createDir( $tempDir );

		 for($j=1; $j<=$depth; $j++){

			  $dir = $this->createDir( $dir.'/Dir'.$j );

		 		for ($i=1; $i<=$imgQuantity; $i++){
			 			$this->createImage(500, 500, 'gif', $dir.'/img'.$i);
		 		}

		 		for ($i=1; $i<=$txtQuantity; $i++){
			 			$this->createTxtFile($dir.'/txt'.$i);
		 		}

	 		}

			return $tempDir;

	 }

	 private function destroyStructure(){

		 foreach ( $this->tmp_files as $file ) {

			 unlink($file);

		 }

		 $this->tmp_files = [];

		 foreach( array_reverse( $this->tmp_dirs ) as $dir ){

				 rmdir($dir);

		 }

		 $this->tmp_dirs = [];

	 }

  /**
  * Loading tests
  */

  /**
  * @dataProvider providerType
  */

  public function testLoadImage($type){

   $image = $this->createImage(1, 1, $type);
   $resizer = ImageResizer::getResizer($image);

   $this->assertInstanceOf('\EKulikova\SingleImageResizer', $resizer);

  }

  public function providerType (){

		$types = array();

		foreach ( array_values( SingleImageResizer::PROPER_TYPES ) as $key=>$type) {

			$types[$key]=[$type];

		}

		return $types;
	}

	public function testLoadDir(){

			$resizer = ImageResizer::getResizer(__DIR__);
			$this->assertInstanceOf('\EKulikova\RecursiveImageResizer', $resizer);

	}

	/**
	 * Bad load tests
	 */

	 /**
	  * @expectedException \EKulikova\ImageResizerException
	  * @expectedExceptionMessage noFile.gif does not exist.
	  */
	 public function testLoadNoFile()
	 {
		 ImageResizer::getResizer('noFile.gif');
	 }

	 /**
	  * @expectedException \EKulikova\ImageResizerException
	  * @expectedExceptionMessage is not an image
	  */
	 public function testLoadNoImage()
	 {
		 ImageResizer::getResizer(__FILE__);
	 }

	 /**
	  * Resize tests
	  */

		/**
 	  * @dataProvider providerResize
 	  */

		public function testResize($height, $width){

		 $image = $this->createImage($width['orig'], $height['orig'], 'gif');
		 $resize = ImageResizer::getResizer($image);

		 $new_image = $resize
				 ->resize($width['new'],$height['new'])
				 ->getImage();

		 $this->assertEquals($width['new'], imagesx($new_image));
		 $this->assertEquals($height['new'], imagesy($new_image));

	 }

	 public function providerResize()
	 {

		 $data = array(
					 array('height'=>['orig'=>200, 'new'=>100],'width'=>['orig'=>100, 'new'=>80]),
				 array('height'=>['orig'=>100, 'new'=>150],'width'=>['orig'=>100, 'new'=>150]),
				 array('height'=>['orig'=>100, 'new'=>50],'width'=>['orig'=>200, 'new'=>120]),
			 );

	 return $data;

	 }

	 /**
		* @dataProvider providerResizeToHeight
		*/

		public function testResizeToHeight($height, $skip_small){

			$image = $this->createImage(200, $height['orig'], 'gif');
			$resize = ImageResizer::getResizer($image);

			$new_image = $resize
				 ->resizeToHeight($height['new'],$skip_small)
				 ->getImage();

			$this->assertEquals(200, imagesx($new_image));
			$this->assertEquals($height['result'], imagesy($new_image));

		}

		public function providerResizeToHeight()
		{
			$data=array(
				 array('height'=>['orig'=>100,'new'=>60,'result'=>60],'skip_small'=>1),
				 array('height'=>['orig'=>100,'new'=>150,'result'=>100],'skip_small'=>1),
				 array('height'=>['orig'=>100,'new'=>130,'result'=>130],'skip_small'=>0),
			);

		 return $data;
	 }

	 /**
	* @dataProvider providerResizeToWidth
	*/

	public function testResizeToWidth($width, $skip_small){

		$image = $this->createImage($width['orig'], 200, 'gif');
		$resize = ImageResizer::getResizer($image);

		$new_image = $resize
				->resizeToWidth($width['new'],$skip_small)
				->getImage();

		$this->assertEquals($width['result'], imagesx($new_image));
		$this->assertEquals(200, imagesy($new_image));
	}

	 public function providerResizeToWidth()
	{

		$data=array(
				 array('width'=>['orig'=>100,'new'=>60,'result'=>60],'skip_small'=>1),
				 array('width'=>['orig'=>100,'new'=>150,'result'=>100],'skip_small'=>1),
				 array('width'=>['orig'=>100,'new'=>130,'result'=>130],'skip_small'=>0),
			);

		return $data;

	}

	/**
	 * @dataProvider providerResizeToHeightWidth
	 */

	 public function testResizeToHeightWidth($width, $height, $skip_small){

		 $image = $this->createImage($width['orig'], $height['orig'], 'gif');
		 $resize = ImageResizer::getResizer($image);

		 $new_image = $resize
					->resizeToHeightWidth($width['new'],$height['new'],$skip_small)
					->getImage();

		 $this->assertEquals($width['result'], imagesx($new_image));
		 $this->assertEquals($height['result'], imagesy($new_image));
	 }

	public function providerResizeToHeightWidth()
	 {

		 $data=array(
					array('width'=>['orig'=>200,'new'=>100,'result'=>100],
						'height'=>['orig'=>500,'new'=>280,'result'=>250],
						'skip_small'=>1),
					array('width'=>['orig'=>100,'new'=>100,'result'=>100],
						'height'=>['orig'=>150,'new'=>250,'result'=>150],
						'skip_small'=>1),
					array('width'=>['orig'=>100,'new'=>200,'result'=>200],
						'height'=>['orig'=>150,'new'=>330,'result'=>300],
						'skip_small'=>0),
			 );

		 return $data;

	 }

	 public function testDirectoryResize(){

		 $dir = $this->createDirectoryStructure(3, 2, 0);

		 $resize = ImageResizer::getResizer($dir);

		 $new_image = $resize->resize(100,100)->save();

		 foreach ( $this->tmp_files as $img ) {

			 list( $width, $height ) = getimagesize( $img );
			 $this->assertEquals(100, $width);
			 $this->assertEquals(100, $height);

		 }

	 }


}
