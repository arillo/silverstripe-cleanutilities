<?php 
/**
 * Provides extra functionality to File.
 *
 * Add this extension to a File instance
 * by adding this to your _config.php:
 * 
 * Object::add_extension('File', 'FileDataExtension');
 * 
 * @package cleanutilities
 * @subpackage utilitiesdecorators
 * 
 * @author arillo
 */
class FileDataExtension extends DataExtension
{
    
    /**
     * Returns the application category this file belongs to.
     * 
     * @return string
     */
    public function appCategory()
    {
        $ext = $this->owner->Extension;
        switch ($ext) {
            case "aif": case "au": case "mid": case "midi": case "mp3": case "ra": case "ram": case "rm":
            case "mp3": case "wav": case "m4a": case "snd": case "aifc": case "aiff": case "wma": case "apl":
            case "avr": case "cda": case "mp4": case "ogg":
                return "audio";
            
            case "mpeg": case "mpg": case "m1v": case "mp2": case "mpa": case "mpe": case "ifo": case "vob":
            case "avi": case "wmv": case "asf": case "m2v": case "qt":
                return "mov";
            
            case "arc": case "rar": case "tar": case "gz": case "tgz": case "bz2": case "dmg": case "jar":
            case "ace": case "arj": case "bz": case "cab":
                return "zip";
                
            case "bmp": case "gif": case "jpg": case "jpeg": case "pcx": case "tif": case "png": case "alpha":
            case "als": case "cel": case "icon": case "ico": case "ps":
                return "image";
            
            case "pdf":
                return "pdf";
        }
    }
    
    /**
     * Reads and returns the creation date of the file. 
     * 
     * @return Datetime
     */
    public function FileCreationDate()
    {
        if (file_exists($this->owner->getFullPath())) {
            return DBField::create_field('Datetime', date("F d Y H:i:s.", filectime($this->owner->getFullPath())));
        }
        return _t('FileDataExtension.NO_DATE', 'No date');
    }
}
