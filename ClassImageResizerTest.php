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

  private $tmp_files = array();

  public function setUp() {
        register_shutdown_function(function() {
          foreach ($this->tmp_files as $value) {
            if(file_exists($value)) {
                unlink($value);
            }
          }
        });
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
         $tmp_file = tempnam(sys_get_temp_dir(), 'resize_test_image');
         array_push($this->tmp_files,$tmp_file);

         return $tmp_file;
     }

     private function addTypesToData($data){

       $rez=array();

       foreach ($this->image_types as $type) {
         foreach($data as $set){
           array_unshift($set,$type);
           array_push($rez,$set);
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
       $resize = new imageResizer($image);

       $this->assertInstanceOf('imageResizer', $resize);
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
      * @expectedException ImageResizerException
      * @expectedExceptionMessage File noFile.gif does not exist
      */
     public function testLoadNoFile()
     {
         new imageResizer('noFile.gif');
     }

     /**
      * @expectedException ImageResizerException
      * @expectedExceptionMessage File ClassImageResizer.php is not an image
      */
     public function testLoadNoImage()
     {
         new imageResizer('ClassImageResizer.php');
     }

     /**
      * Resize tests
      */

      /**
       * @dataProvider providerResize
       */

      public function testResize($type, $orig_height, $new_height, $orig_width, $new_width){

        $image = $this->createImage($orig_width, $orig_height, $type);
        $resize = new imageResizer($image);

        $new_image=$resize->resize($new_width,$new_height);

        $this->assertEquals($new_width, imagesx($new_image));
        $this->assertEquals($new_height, imagesy($new_image));

      }

      public function providerResize()
       {
         // data [orig_height,new_height,orig_width,new_width]
         $data=array(
                 [200,100,100,80],
                 [100,150,100,150],
                 [100,50,200,120]
             );

         return $this->addTypesToData($data);

       }

      /**
       * @dataProvider providerResizeToHeight
       */

      public function testResizeToHeight($type, $orig_height,$height,$skip_small,$new_height){

        $image = $this->createImage(200, $orig_height, $type);
        $resize = new imageResizer($image);

        $new_image=$resize->resizeToHeight($height,$skip_small);

        $this->assertEquals(200, imagesx($new_image));
        $this->assertEquals($new_height, imagesy($new_image));

      }

      public function providerResizeToHeight()
       {
         // data [orig_height,height,skip_small,new_height]
         $data=array(
                 [100,60,1,60],
                 [100,150,1,100],
                 [100,130,0,130]
             );

         return $this->addTypesToData($data);

       }

       /**
        * @dataProvider providerResizeToWidth
        */

       public function testResizeToWidth($type, $orig_width,$width,$skip_small,$new_width){

         $image = $this->createImage($orig_width, 200, $type);
         $resize = new imageResizer($image);

         $new_image=$resize->resizeToWidth($width,$skip_small);

         $this->assertEquals($new_width, imagesx($new_image));
         $this->assertEquals(200, imagesy($new_image));

       }

       public function providerResizeToWidth()
        {
          // data [orig_width,width,skip_small,new_width]
          $data=array(
                  [100,60,1,60],
                  [100,150,1,100],
                  [100,130,0,130]
              );

          return $this->addTypesToData($data);

        }

        /**
         * @dataProvider providerResizeToHeightWidth
         */

        public function testResizeToHeightWidth($type,$orig_width,$orig_height,$width,$height,$skip_small,$new_width,$new_height){

          $image = $this->createImage($orig_width, 200, $type);
          $resize = new imageResizer($image);

          $new_image=$resize->resizeToWidth($width,$skip_small);

          $this->assertEquals($new_width, imagesx($new_image));
          $this->assertEquals(200, imagesy($new_image));

        }

        public function providerResizeToHeightWidth()
         {
           // data [orig_width, orig_height,width, height,skip_small,new_width,new_height]
           $data=array(
                   [200,500,100,280,1,100,250],
                   [100,150,100,250,1,100,150],
                   [100,150,200,330,0,200,300]
               );

           return $this->addTypesToData($data);

         }

}
