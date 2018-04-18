<?php
namespace EKulikova;

class SingleImageResizer{

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

		$this->originPath=$originPath;

		$this -> setImageInfo();

		$this -> loadImage();

	}

	public function __destruct(){
		imagedestroy($this->image);
	}

	private function setImageInfo(){

		list($this->width, $this->height, $this->MIMEtype) = getimagesize($this->originPath);

		if(!$this->width || !$this->height){
			throw new ImageResizerException('File '.$this->originPath.' is not an image');
		}

		if ( array_key_exists( $this->MIMEtype, self::PROPER_TYPES ) ) {

				$this->type = self::PROPER_TYPES[$this->MIMEtype];

		} else {

				throw new ImageResizerException('Unsupported image type. File '.$this->originPath);

		}

	}

	private function loadImage(){

			$createFunction = 'imagecreatefrom'.$this->type;

			$this->image = $createFunction( $this->originPath );

			if (!$this->image) {
	        throw new ImageResizerException('Could not load image from '.$this->originPath);
	    }

	}

	private function update($new_image){

		$this->image=$new_image;
		$this->height=imagesy($this->image);
		$this->widht=imagesx($this->image);

	}

	public function getImage(){
		return $this->image;
	}

	public function output($filename=null, $compression=75){

		if( $this->MIMEtype == IMAGETYPE_JPEG ) {
			imagejpeg($this->image,$filename,$compression);
		} elseif( $this->MIMEtype == IMAGETYPE_GIF ) {
			imagegif($this->image,$filename);
		} elseif( $this->MIMEtype == IMAGETYPE_PNG ) {
			imagepng($this->image,$filename);
		} else {
			throw new ImageResizerException('Could not output image '.$this->originPath);
		}

	}

	public function save($filename=null, $permissions=0777, $compression=75){

		$filename or $filename=$this->originPath;

		$this -> output($filename, $compression);

		if($permissions) {
			chmod($filename,$permissions);
		}

	}

	public function resize($new_width,$new_height){

		$new_image = imagecreatetruecolor($new_width, $new_height);
		imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $new_width, $new_height, $this->width, $this->height);
		$this->update($new_image);

		return $this;

	}

	public function resizeToHeight($new_height,$skip_small=1){

		if($skip_small && $this->height<=$new_height){
			return $this;
		}
		else{
			$ratio = $this->height/$new_height;
			$new_height=$this->height/$ratio;

			$this->resize($this->width,$new_height);
		}

		return $this;

	}

	public function resizeToWidth($new_width,$skip_small=1){

		if($skip_small && $this->width<=$new_width){
			return $this;
		}
		else{
			$ratio = $this->width/$new_width;
			$new_width=$this->width/$ratio;

			$this->resize($new_width,$this->height);
		}

		return $this;

	}

	public function resizeToHeightWidth($new_width,$new_height,$skip_small=1){

		if($skip_small && $this->width<=$new_width && $this->height<=$new_height){
			return $this;
		}
		else{
			$ratio = max($this->width/$new_width,$this->height/$new_height);
			$new_width=$this->width/$ratio;
			$new_height=$this->height/$ratio;

			$this->resize($new_width,$new_height);
		}

		return $this;

	}

}

class ImageResizerException extends \Exception
{
}
