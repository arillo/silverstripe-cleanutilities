<?php
/**
 * Provides extra functionality to to all kinds of Assets, mainly File subclasses.
 *
 * Add this extension to a Controller instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('File', 'AssetsDecorator');
 *
 * @package cleanutilities
 * @subpackage decorators
 *
 * @author arillo
 */
class AssetsDecorator extends DataObjectDecorator {

	/**
	 * Max. file count in controlled upload folders.
	 *
	 * @var int
	 */
	static $maxfilesperfolder = 100;

	/**
	 * Limits the count of files in a folder to $maxfilesperfolder.
	 * Automatically adds new subfolders.
	 *
	 * @param string $foldername
	 * @return string
	 */
	public static function ControlledUploadFolder($foldername = '/uploads/'){
		$folder = ASSETS_PATH.$foldername;
		Folder::findOrMake($foldername);
		$dir = opendir($folder);
		$lastfolder = '';
		$foldercount = 0;
		while($file = readdir($dir)) {
			if(substr($file,0,1) != '_' && $file != '.' && $file != '..' && is_dir($folder.$file)) {
				$filecount = count(glob($folder.$file."/*.*"));
				$lastfolder = $folder.$file;
				$foldercount++;
				if($filecount < AssetsDecorator::$maxfilesperfolder){
					break;
				}
			}
		}
		if($lastfolder == ""){
			$newfolder = Folder::findOrMake($foldername.'000000');
			return $foldername.$newfolder->Name;
		}

		$filecount = count(glob($lastfolder."/*.*"));
		if($filecount < AssetsDecorator::$maxfilesperfolder){
			return $foldername.$file;
		} else {
			$onemore = str_pad($foldercount, 6, "0", STR_PAD_LEFT);
			$newfolder = Folder::findOrMake($foldername.$onemore);
			return $foldername.$newfolder->Name;
		}
	}

	/**
	 * A folder name compund [$ClassName] / [$ID]
	 *
	 * @return string
	 */
	public function HomeDirectory(){ return '/'.$this->owner->ClassName.'/' . $this->owner->ID; }
}