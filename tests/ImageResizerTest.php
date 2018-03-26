<?php

require_once __DIR__.'/../lib/ImageResizer.php';

use EKulikova\ImageResizer;
use EKulikova\ImageResizerException;


if (version_compare(PHP_VERSION, '7.0.0') >= 0 && !class_exists('PHPUnit_Framework_TestCase')) {
	class_alias('PHPUnit\Framework\TestCase', 'PHPUnit_Framework_TestCase');
}

class ImageResizerTest extends PHPUnit_Framework_TestCase
{

	private $image_types = array(
		'gif',
		'jpeg',
		'png'
	);

	private $tmp_files = array();

	protected function tearDown(){
			$this->destroyImages();
	}

	private function destroyImages(){

		foreach ($this->tmp_files as $file) {
			if(file_exists($file)) {
				unlink($file);
			}
		}

	}

	/**
	 * Helpers
	 */

	 private function createImage($width, $height, $type)
	 {

		 if (!in_array($type, $this->image_types)) {
			 throw new ImageResizerException('Unsupported image type');
		 }

		 $image = imagecreatetruecolor($width, $height);

		 $filename = $this->getTempFile();

		 $output_function = 'image' . $type;
		 $output_function($image, $filename);

		 return $filename;
	 }

	 private function getTempFile()
	 {
		 $tmp_file = tempnam(sys_get_temp_dir(), 'resize_test_image');
		 array_push($this->tmp_files,$tmp_file);

		 return $tmp_file;
	 }

	 private function addTypesToData($data){

	   $rez=array();

	   foreach ($this->image_types as $type) {
		   foreach($data as $param){
			   $param['type'] = $type;
			   array_push($rez,array($param));
		   }
	   }

	   return $rez;

	 }

   /**
	* Loading tests
	*/

   /**
	* @dataProvider providerType
	*/

	public function testLoad($type)
	{

		$image = $this->createImage(1, 1, $type);
		$resize = new ImageResizer($image);

		$this->assertInstanceOf('\EKulikova\ImageResizer', $resize);

	}

	public function providerType ()
	{

		$types=array();

		foreach ($this->image_types as $key=>$type) {
			$types[$key]=[$type];
		}

		return $types;
	}

	/**
	 * Bad load tests
	 */

	 /**
	  * @expectedException \EKulikova\ImageResizerException
	  * @expectedExceptionMessage File noFile.gif does not exist
	  */
	 public function testLoadNoFile()
	 {
		 new ImageResizer('noFile.gif');
	 }

	 /**
	  * @expectedException \EKulikova\ImageResizerException
	  * @expectedExceptionMessage is not an image
	  */
	 public function testLoadNoImage()
	 {
		 new ImageResizer(__FILE__);
	 }

	 /**
	  * Resize tests
	  */

	 /**
	  * @dataProvider providerResize
	  */

	  public function testResize($param){

		  $image = $this->createImage($param['width']['orig'], $param['height']['orig'], $param['type']);
		  $resize = new ImageResizer($image);

		  $new_image=$resize->resize($param['width']['new'],$param['height']['new']);

		  $this->assertEquals($param['width']['new'], imagesx($new_image));
		  $this->assertEquals($param['height']['new'], imagesy($new_image));

	  }

	  public function providerResize()
	  {

		  $data = array(
			  		array('height'=>['orig'=>200, 'new'=>100],'width'=>['orig'=>100, 'new'=>80]),
					array('height'=>['orig'=>100, 'new'=>150],'width'=>['orig'=>100, 'new'=>150]),
					array('height'=>['orig'=>100, 'new'=>50],'width'=>['orig'=>200, 'new'=>120]),
				);

		return $this->addTypesToData($data);

		}

	  /**
	   * @dataProvider providerResizeToHeight
	   */

	   public function testResizeToHeight($param){

		   $image = $this->createImage(200, $param['height']['orig'], $param['type']);
		   $resize = new ImageResizer($image);

		   $new_image=$resize->resizeToHeight($param['height']['set_value'],$param['skip_small']);

		   $this->assertEquals(200, imagesx($new_image));
		   $this->assertEquals($param['height']['new'], imagesy($new_image));

	   }

	   public function providerResizeToHeight()
	   {
		   $data=array(
				  array('height'=>['orig'=>100,'set_value'=>60,'new'=>60],'skip_small'=>1),
				  array('height'=>['orig'=>100,'set_value'=>150,'new'=>100],'skip_small'=>1),
				  array('height'=>['orig'=>100,'set_value'=>130,'new'=>130],'skip_small'=>0),
			 );

			return $this->addTypesToData($data);
		}

	   /**
		* @dataProvider providerResizeToWidth
		*/

		public function testResizeToWidth($param){

			$image = $this->createImage($param['width']['orig'], 200, $param['type']);
			$resize = new ImageResizer($image);

			$new_image=$resize->resizeToWidth($param['width']['set_value'],$param['skip_small']);

			$this->assertEquals($param['width']['new'], imagesx($new_image));
			$this->assertEquals(200, imagesy($new_image));
		}

	   public function providerResizeToWidth()
		{

		  $data=array(
				   array('width'=>['orig'=>100,'set_value'=>60,'new'=>60],'skip_small'=>1),
				   array('width'=>['orig'=>100,'set_value'=>150,'new'=>100],'skip_small'=>1),
				   array('width'=>['orig'=>100,'set_value'=>130,'new'=>130],'skip_small'=>0),
			  );

		  return $this->addTypesToData($data);

		}

		/**
		 * @dataProvider providerResizeToHeightWidth
		 */

		 public function testResizeToHeightWidth($param){

			 $image = $this->createImage($param['width']['orig'], $param['height']['orig'], $param['type']);
			 $resize = new ImageResizer($image);

			 $new_image=$resize->resizeToHeightWidth($param['width']['set_value'],$param['height']['set_value'],$param['skip_small']);

			 $this->assertEquals($param['width']['new'], imagesx($new_image));
			 $this->assertEquals($param['height']['new'], imagesy($new_image));
		 }

		public function providerResizeToHeightWidth()
		 {

		   $data=array(
					  array('width'=>['orig'=>200,'set_value'=>100,'new'=>100],
							'height'=>['orig'=>500,'set_value'=>280,'new'=>250],
							'skip_small'=>1),
					  array('width'=>['orig'=>100,'set_value'=>100,'new'=>100],
							'height'=>['orig'=>150,'set_value'=>250,'new'=>150],
							'skip_small'=>1),
					  array('width'=>['orig'=>100,'set_value'=>200,'new'=>200],
							'height'=>['orig'=>150,'set_value'=>330,'new'=>300],
							'skip_small'=>0),
			   );

		   return $this->addTypesToData($data);

		 }

		 /**
	 * Save and Output functions test
	 */

	 public function testSave(){

			$image = $this->createImage(500, 500, 'gif');

			$resize = new ImageResizer($image);
			$resize->resize(100,100);

			$filename = $this->getTempFile();
			$resize->save( $filename );

			list($width, $height, $type) = getimagesize( $filename );

			$this->assertEquals(100, $width);
			$this->assertEquals(100, $height);
			$this->assertEquals(IMAGETYPE_GIF, $type);

	 }

}
