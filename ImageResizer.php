<?php
namespace EKulikova;

class ImageResizer{
	private $image;
	private $source;
	private $width;
	private $height;
	private $type;

	private $properTypes = [
		'IMAGETYPE_JPEG' => 'jpeg',
		'IMAGETYPE_GIF' => 'gif',
		'IMAGETYPE_PNG' => 'png',
	];


	public function __construct($source){

		$this -> setSource($source);

		$this -> setSize();

		$this -> setType();

		/*if ( array_key_exists( $this->type, $this->properTypes ) ) {

				$this -> createImage();

		} else {

				throw new ImageResizerException('Unsupported image type. File '.$source);

		}*/

		if( $this->type == IMAGETYPE_JPEG ) {
			$this->image = imagecreatefromjpeg($source);
		} elseif( $this->type == IMAGETYPE_GIF ) {
			$this->image = imagecreatefromgif($source);
		} elseif( $this->type == IMAGETYPE_PNG ) {
			$this->image = imagecreatefrompng($source);
		} else {
				throw new ImageResizerException('Unsupported image type. File '.$source);
		}

		if (!$this->image) {
            throw new ImageResizerException('Could not load image from '.$source);
        }

	}

	public function __destruct(){
		imagedestroy($this->image);
	}

	private function setSource($source){

			if (!is_file($source)) {
					 throw new ImageResizerException('File '.$source.' does not exist');
			}

			$this->source=$source;

	}

	private function setSize(){

		list($this->width, $this->height) = getimagesize($this->source);

		if(!$this->width || !$this->height){
			throw new ImageResizerException('File '.$this->source.' is not an image');
		}

	}

	private function setType(){

		$this->type = exif_imagetype($this->source);

		

	}

	/*private function createImage(){

			$createFunction = 'imagecreatefrom'.

	}*/

	private function update($new_image){

		$this->image=$new_image;
		$this->height=imagesy($this->image);
		$this->widht=imagesx($this->image);

	}

	private function save($filename,$compression=75,$permissions=0777){

		$filename or $filename=$this->source;

		if( $this->type == IMAGETYPE_JPEG ) {
			imagejpeg($this->image,$filename,$compression);
		} elseif( $this->type == IMAGETYPE_GIF ) {
			imagegif($this->image,$filename);
		} elseif( $this->type == IMAGETYPE_PNG ) {
			imagepng($this->image,$filename);
		}

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
