<?php
/**
 * Provides extra functionality to Image classes.
 *
 * Add this extension to a Image instance
 * by adding this to your _config.php:
 * 
 * Object::add_extension('Image', 'ImageDataExtension');
 * 
 * @package cleanutilities
 * @subpackage data_extensions
 * 
 * @author arillo
 */
class ImageDataExtension extends DataExtension
{

    /**
     * Fixes image orientation php bugs, caused by some digital photo devices (e.g. Iphone)
     * 
     * @param  Image  $image
     * @return boolean Indicator for if the image was changed.
     */
    public static function fix_orientation(Image $image)
    {
        if (!$image instanceof Image) {
            return user_error('$image must be an instance of Image!');
        }

        $filename = Controller::join_links(
            BASE_PATH,
            $image->Filename
        );

        //if (!file_exists($filename)) return false;
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        // exif is supported by jpgs only
        if (($extension == 'jpg' || $extension == 'jpeg')) {
            $exif = exif_read_data($filename);
            $sourceImage = false;
            $destinationImage = false;
            $sourceImage = imagecreatefromjpeg($filename);

            if (isset($exif['Orientation'])
                && $sourceImage
            ) {
                $orientation = $exif['Orientation'];
                //Debug::show($orientation);
                switch ($orientation) {
                    case 2: // horizontal flip
                        $destinationImage = $sourceImage;
                        $this->image_flip($destinationImage);
                        break;
                    case 3: // 180 rotate left
                        $destinationImage = imagerotate($sourceImage, 180, -1);
                        break;
                    case 4: // vertical flip
                        $this->image_flip($dimg);
                        break;
                    case 5: // vertical flip + 90 rotate right
                        $this->image_flip($sourceImage);
                        $destinationImage = imagerotate($sourceImage, -90, -1);
                        break;
                    case 6: // 90 rotate right
                        $destinationImage = imagerotate($sourceImage, -90, 0);
                        break;
                    case 7: // horizontal flip + 90 rotate right
                        $this->image_flip($destinationImage);
                        $destinationImage = imagerotate($destinationImage, -90, -1);
                        break;
                    case 8: // 90 rotate left
                        $destinationImage = imagerotate($sourceImage, 90, -1);
                        break;
                }
                // Output
                if ($destinationImage) {
                    imagejpeg($destinationImage, $filename);
                    // Free the memory
                    imagedestroy($sourceImage);
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Flips an image by mirroring integer from left to right
     * 
     * @param  resource  $image
     * @param  integer $x
     * @param  integer $y
     * @param  integer  $width
     * @param  integer  $height
     * @return boolean
     */
    public static function image_flip(&$image, $x = 0, $y = 0, $width = null, $height = null)
    {
        if ($width < 1) {
            $width  = imagesx($image);
        }
        if ($height < 1) {
            $height = imagesy($image);
        }
        // Truecolor provides better results, if possible.
        if (function_exists('imageistruecolor') && imageistruecolor($image)) {
            $tmp = imagecreatetruecolor(1, $height);
        } else {
            $tmp = imagecreate(1, $height);
        }
        $x2 = $x + $width - 1;
        for ($i = (int)floor(($width - 1) / 2); $i >= 0; $i--) {
            // Backup right stripe.
            imagecopy($tmp, $image, 0, 0, $x2 - $i, $y, 1, $height);
            // Copy left stripe to the right.
            imagecopy($image, $image, $x2 - $i, $y, $x + $i, $y, 1, $height);
            // Copy backuped right stripe to the left.
            imagecopy($image, $tmp, $x + $i,  $y, 0, 0, 1, $height);
        }
        imagedestroy($tmp);
        return true;
    }

    /**
     * Class instance wrapper for fix_orientation()
     * 
     * @return boolean Indicator for if the image was changed
     */
    public function fixOrientation()
    {
        return self::fix_orientation($this->owner);
    }

    /**
     * Returns half of image width.
     * 
     * @return int
     */
    public function getHalfWidth()
    {
        return ($this->owner->getDimensions(0)/2);
    }
    
    /**
     * @param int $width
     * @param int $height
     * @return Image
     */
    public function SetMaxRatioSize($width, $height)
    {
        if ($this->owner->Width <= $width && $this->owner->Height <= $height) {
            return $this->owner;
        }
        return $this->owner->getFormattedImage('SetRatioSize', $width, $height);
    }

    /**
     * @param int $width
     * @return Image
     */
    public function SetMaxWidth($width)
    {
        if ($this->owner->Width <= $width) {
            return $this->owner;
        }
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
    public function ColorizeImage($tint_r = 255, $tint_g = 255, $tint_b = 255)
    {
        if ($this->owner->ID
            && $this->owner->Filename
            && Director::fileExists($this->owner->Filename)
        ) {
            $cacheFile = $this->owner->cacheFilename("ColorizedImage", $tint_r, $tint_g, $tint_b);
            if (!file_exists(Director::baseFolder().'/'.$cacheFile)
                || isset($_GET['flush'])
            ) {
                $this->setColoredImage($tint_r, $tint_g, $tint_b);
            }
            $coloredimage = Image::create();
            $coloredimage->setFilename($cacheFile);
            $coloredimage->ID = 1;
            return $coloredimage;
        }
    }

    public function setColoredImage($tint_r = 255, $tint_g = 255, $tint_b = 255)
    {
        $cacheFile = Director::baseFolder().'/'.$this->owner->cacheFilename("ColorizedImage", $tint_r, $tint_g, $tint_b);
        $gd = new CleanGD(Director::baseFolder().'/'.$this->owner->Filename);
        if ($gd->hasImageResource()) {
            $gd = $gd->imagetint($gd, $tint_r, $tint_g, $tint_b);
            if ($gd) {
                $gd->writeTo($cacheFile);
            }
        }
    }
    
    /**
     * Generates an image with colored padding.
     * 
     * @param int $width
     * @param int $height
     * @param string $color hex color like FF0000
     * @return Image
     */
    public function PaddedImageWithColor($width, $height, $color)
    {
        if ($this->owner->ID && $this->owner->Filename
            && Director::fileExists($this->owner->Filename)
        ) {
            $cacheFile = $this->owner->cacheFilename("PaddedImageWithColorImage", $width, $height, $color);
            if (!file_exists(Director::baseFolder().'/'.$cacheFile)
                || isset($_GET['flush'])
            ) {
                $this->setPaddedImageWithColor($width, $height, $color);
            }
            $image = new Image();
            $image->setFilename($cacheFile);
            $image->ID = 1;
            return $image;
        }
    }

    public function setPaddedImageWithColor($width, $height, $color)
    {
        $cacheFile = Director::baseFolder().'/'.$this->owner->cacheFilename("PaddedImageWithColorImage", $width, $height, $color);
        $gd = new CleanGD(Director::baseFolder().'/'.$this->owner->Filename);
        if ($gd->hasGD()) {
            $gd = $gd->paddedResize($width, $height, $color);
            if ($gd) {
                $gd->writeTo($cacheFile);
            }
        }
    }
    
    /**
     * Rotate an image by angle. 
     * 
     * @param int $angle
     * @return Image
     */
    public function RotatedImage($angle)
    {
        return $this->owner->getFormattedImage('RotatedImage', $angle);
    }

    public function generateRotatedImage(GD $gd, $angle)
    {
        return $gd->rotate($angle);
    }
}
