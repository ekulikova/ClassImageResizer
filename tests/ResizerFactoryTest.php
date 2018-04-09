<?php

require_once __DIR__.'/../lib/ResizerFactory.php';

use EKulikova\ResizerFactory;

if (version_compare(PHP_VERSION, '7.0.0') >= 0 && !class_exists('PHPUnit_Framework_TestCase')) {
	class_alias('PHPUnit\Framework\TestCase', 'PHPUnit_Framework_TestCase');
}

class ResizerFactoryTest extends PHPUnit_Framework_TestCase
{

	private $tmp_file;

	protected function tearDown(){

		if(file_exists($this->tmp_file)) {

			unlink($this->tmp_file);

		}

	}

	/**
	 * Helpers
	 */

	 private function createImage($width, $height)
	 {

		 $image = imagecreatetruecolor($width, $height);

		 $filename = $this->getTempFile();

		 $output_function = 'imagejpeg';
		 $output_function($image, $filename);

		 return $filename;
	 }

	 private function getTempFile()
	 {
		 $this->tmp_file = tempnam(sys_get_temp_dir(), 'resize_test_image');

		 return $this->tmp_file;
	 }

  /**
  * Test
  */

  public function testGetResizer(){

			$img = $this->createImage(100, 100);

			$resizer1 = ResizerFactory::getResizer($img);

			$this->assertInstanceOf('\EKulikova\ImageResizer', $resizer1);


			$resizer2 = ResizerFactory::getResizer(__DIR__);

			$this->assertInstanceOf('\EKulikova\RecursiveImageResizer', $resizer2);
  }

}
