<?php
namespace EKulikova;

require_once 'IResizer.php';

class ImageResizer implements iResizer{

	const PROPER_TYPES = [
		IMAGETYPE_JPEG => 'jpeg',
		IMAGETYPE_GIF => 'gif',
		IMAGETYPE_PNG => 'png',
	];

	private $image;
	private $originPath;
	private $width;
	private $height;
	private $MIMEtype;
	private $type; // self::PROPER_TYPES[$this->MIMEtype]

	public function __construct($originPath){

		$this -> setOriginPath($originPath);

		$this -> setImageInfo();

		$this -> createImage();

	}

	public function __destruct(){
		imagedestroy($this->image);
	}

	private function setOriginPath($originPath){

			if (!is_file($originPath)) {
					 throw new ImageResizerException('File '.$originPath.' does not exist');
			}

			$this->source=$originPath;

	}

	private function setImageInfo(){

		list($this->width, $this->height, $this->MIMEtype) = getimagesize($this->source);

		if(!$this->width || !$this->height){
			throw new ImageResizerException('File '.$this->source.' is not an image');
		}

		if ( array_key_exists( $this->MIMEtype, self::PROPER_TYPES ) ) {

				$this->type = self::PROPER_TYPES[$this->MIMEtype];

		} else {

				throw new ImageResizerException('Unsupported image type. File '.$this->source);

		}

	}

	private function createImage(){

			$createFunction = 'imagecreatefrom'.$this->type;

			$this->image = $createFunction( $this->source );

			if (!$this->image) {
	        throw new ImageResizerException('Could not load image from '.$this->source);
	    }

	}

	private function update($new_image){

		$this->image=$new_image;
		$this->height=imagesy($this->image);
		$this->widht=imagesx($this->image);

	}

	public function imageOutput($filename, $compression){

		if( $this->MIMEtype == IMAGETYPE_JPEG ) {
			imagejpeg($this->image,$filename,$compression);
		} elseif( $this->MIMEtype == IMAGETYPE_GIF ) {
			imagegif($this->image,$filename);
		} elseif( $this->MIMEtype == IMAGETYPE_PNG ) {
			imagepng($this->image,$filename);
		} else {
			throw new ImageResizerException('Could not output image '.$this->source);
		}

		return $this->image;

	}

	public function save($filename=null, $permissions=0777, $compression=75){

		$filename or $filename=$this->source;

		$this -> imageOutput($filename, $compression);

		if($permissions) {
			chmod($filename,$permissions);
		}

	}

	public function resize($new_width,$new_height){

		$new_image = imagecreatetruecolor($new_width, $new_height);
		imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $new_width, $new_height, $this->width, $this->height);
		$this->update($new_image);

		return $this->image;

	}

	public function resizeToHeight($h,$skip_small=1){

		if($skip_small && $this->height<=$h){
			return $this->image;
		}
		else{
			$ratio = $this->height/$h;
			$new_height=$this->height/$ratio;

			$this->resize($this->width,$new_height);
		}

		return $this->image;

	}

	public function resizeToWidth($w,$skip_small=1){

		if($skip_small && $this->width<=$w){
			return $this->image;
		}
		else{
			$ratio = $this->width/$w;
			$new_width=$this->width/$ratio;

			$this->resize($new_width,$this->height);
		}

		return $this->image;

	}

	public function resizeToHeightWidth($w,$h,$skip_small=1){

		if($skip_small && $this->width<=$w && $this->height<=$h){
			return $this->image;
		}
		else{
			$ratio = max($this->width/$w,$this->height/$h);
			$new_width=$this->width/$ratio;
			$new_height=$this->height/$ratio;

			$this->resize($new_width,$new_height);
		}

		return $this->image;

	}

}

class ImageResizerException extends \Exception
{
}
