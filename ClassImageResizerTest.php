<?php

require_once 'ClassImageResizer.php';

if (version_compare(PHP_VERSION, '7.0.0') >= 0 && !class_exists('PHPUnit_Framework_TestCase')) {
    class_alias('PHPUnit\Framework\TestCase', 'PHPUnit_Framework_TestCase');
}

class imageResizerTest extends PHPUnit_Framework_TestCase
{

  private $image_types = array(
      'gif',
      'jpeg',
      'png'
  );

  /**
   * Loading tests
   */

   /**
    * @dataProvider providerLoadType
    */

   public function testLoad($type)
   {
       $image = $this->createImage(1, 1, $type);
       $resize = new imageResizer($image);

       $this->assertInstanceOf('imageResizer', $resize);
   }

   public function providerLoadType ()
    {
      $types=array();

      foreach ($this->image_types as $key=>$type) {
          $types[$key]=[$type];
      }
      
      return $types;
    }

   /**
    * Helpers
    */

    private function createImage($width, $height, $type)
    {
        if (!in_array($type, $this->image_types)) {
            throw new ImageResizeException('Unsupported image type');
        }

        $image = imagecreatetruecolor($width, $height);

        $filename = $this->getTempFile();

        $output_function = 'image' . $type;
        $output_function($image, $filename);

        return $filename;
    }

    private function getTempFile()
    {
        return tempnam(sys_get_temp_dir(), 'resize_test_image');
    }

}
