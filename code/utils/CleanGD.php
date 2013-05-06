<?php
/**
 * Provides extra funtionality to GD
 * 
 * @package cleanutilities
 * @subpackage utils
 * 
 * @author arillo
 */
class CleanGD extends GD{
		/*
		Here's some examples:

		imagegrayscaletint ($img);  // Grayscale, no tinting
		imagegrayscaletint ($img,304,242,209);  // What I use for sepia
		imagegrayscaletint ($img,0,0,255);  // A berry blue image

		The RGB values for tinting are normally from 0 to 255.  But, you can use values larger than 255 to lighten and "burn" the image.  The sepia example above does this a little, the below example provides a better example of lightening the image and burning the light areas out a little:

		imagegrayscaletint ($img,400,400,400);  // Lighten image
		imagegrayscaletint ($img,127,127,127);  // Darken image
		*/
	function imagetint(&$img, $tint_r = 255, $tint_g = 255, $tint_b = 255){
		if(!$this->gd) return;
		$width = imagesx($this->gd); 
		$height = imagesy($this->gd);
		$dest = imagecreate ($width, $height);
		//		  $dest = imagecreatetruecolor($width, $height);
		for ($i=0; $i<256; $i++) imagecolorallocate ($dest, $i, $i, $i);
		imagecopyresized ($dest, $this->gd, 0, 0, 0, 0, $width, $height, $width, $height);
		for ($i = 0; $i < 256; $i++) imagecolorset ($dest, $i, min($i * abs($tint_r) / 255, 255), min($i * abs($tint_g) / 255, 255), min($i * abs($tint_b) / 255, 255));
		$img = imagecreate ($width, $height);
		imagecopy ($this->gd, $dest, 0, 0, 0, 0, $width, $height);
		imagedestroy ($dest);
		$output = clone $this;
		$output->setGD($this->gd);
		return $output;
	}
}