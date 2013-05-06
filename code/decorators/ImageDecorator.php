<?php
/**
 * Provides extra functionality to Image classes.
 *
 * Add this extension to a Image instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('Imgae', 'ImageDecorator');
 *
 * @package cleanutilities
 * @subpackage decorators
 *
 * @author arillo
 */
class ImageDecorator extends DataObjectDecorator{


	/**
	 * Returns half of image width.
	 *
	 * @return int
	 */
	function getHalfWidth(){ return ($this->owner->getDimensions(0)/2); }

	/**
	 * @param int $width
	 * @param int $height
	 * @return Image
	 */
	public function SetMaxRatioSize($width, $height){
		if($this->owner->Width <= $width && $this->owner->Height <= $height) return $this->owner;
		return $this->owner->getFormattedImage('SetRatioSize', $width, $height);
	}

	/**
	 * @param int $width
	 * @return Image
	 */
	function SetMaxWidth($width){
		if($this->owner->Width <= $width) return $this->owner;
		return $this->owner->SetWidth($width);
	}

	/**
	 * Created a tinted version of this image.
	 *
	 * @param int $tint_r
	 * @param int $tint_g
	 * @param int $tint_b
	 * @return Image
	 */
	function ColorizeImage($tint_r = 255, $tint_g = 255, $tint_b = 255){
		if($this->owner->ID && $this->owner->Filename && Director::fileExists($this->owner->Filename)){
			$cacheFile = $this->owner->cacheFilename("ColorizedImage", $tint_r, $tint_g, $tint_b);
			if(!file_exists(Director::baseFolder().'/'.$cacheFile) || isset($_GET['flush'])){
				$this->setColoredImage($tint_r, $tint_g, $tint_b);
			}
			$coloredimage = new Image();
			$coloredimage->setFilename($cacheFile);
			$coloredimage->ID = 1;
			return $coloredimage;
		}
	}

	function setColoredImage($tint_r = 255, $tint_g = 255, $tint_b = 255){
		$cacheFile = Director::baseFolder().'/'.$this->owner->cacheFilename("ColorizedImage", $tint_r, $tint_g, $tint_b);
		$gd = new CleanGD(Director::baseFolder().'/'.$this->owner->Filename);
		if($gd->hasGD()){
			$gd = $gd->imagetint($gd, $tint_r, $tint_g, $tint_b);
			if($gd) $gd->writeTo($cacheFile);
		}
	}

	/**
	 * Generates an image with colored padding.
	 *
	 * @param int $width
	 * @param int $height
	 * @param int $color
	 * @return Image
	 */
    public function PaddedImageWithColor($width, $height, $color){
    	if($this->owner->ID && $this->owner->Filename && Director::fileExists($this->owner->Filename)){
			$cacheFile = $this->owner->cacheFilename("PaddedImageWithColorImage", $width, $height, $color);
			if(!file_exists(Director::baseFolder().'/'.$cacheFile) || isset($_GET['flush'])){
				$this->setPaddedImageWithColor($width,$height,$color);
			}
			$image = new Image();
			$image->setFilename($cacheFile);
			$image->ID = 1;
			return $image;
		}
	}

	function setPaddedImageWithColor($width, $height, $color){
		$cacheFile = Director::baseFolder().'/'.$this->owner->cacheFilename("PaddedImageWithColorImage", $width, $height, $color);
		$gd = new CleanGD(Director::baseFolder().'/'.$this->owner->Filename);
		if($gd->hasGD()){
			$gd = $gd->paddedResize($width, $height, $color);
			if($gd) $gd->writeTo($cacheFile);
		}
	}

	/**
	 * Rotate an image by angle.
	 *
	 * @param int $angle
	 * @return Image
	 */
	public function RotatedImage($angle){
		return $this->owner->getFormattedImage('RotatedImage', $angle);
	}
	public function generateRotatedImage(GD $gd, $angle){
		return $gd->rotate($angle);
	}

}