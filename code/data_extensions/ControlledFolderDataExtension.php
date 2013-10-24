<?php
/**
 * Provides the extended class with the ability
 * to use controlled upload folders. Controlled in this case means,
 * the amount of files contained in each folder is limited to the value of
 * ControlledFolderDataExtension::$folder_max_files. It will create subfolders
 * named in a numeric way like 000000 to 999999.
 *
 * Add this extension to a File instance
 * by adding this to your _config.php:
 *
 * // For Clean Models this is already done in the modules _config.php
 * DataObject::add_extension('CleanFile', 'ControlledFolderDataExtension');
 * // With this configuration, ControlledFolderDataExtension::$folder_max_files will be used as
 * // limit and the folder will be named by the related class name ("CleanFile") in this example.
 * // 
 * // You also can pass a config array to this method to override the defaults, like this:
 * ControlledFolderDataExtension::set_controlled_folder_for(
 *   "CleanFile",
 *   array(
 *    'folderName' => "MyFolder",
 *    'folderMaxFiles' => 23
 * ));
 *
 * This extension adds an instance function to the decorated class e.g.
 * $cleanFile = CleanFile::create();
 * // for using a controlled upload folder with default/ earlier created settings
 * $cleanFile->getUploadFolder();
 * // you can also pass a config object like:
 * $cleanFile->getUploadFolder(
 *  array(
 *    'folderName' => "MyFolder",
 *    'folderMaxFiles' => 23
 *  ),
 *  true // this flag determines if this config should be made permanent for later use.
 * );
 * 
 * 
 * @package cleanutilities
 * @subpackage data_extensions
 * 
 * @author arillo
 */
class ControlledFolderDataExtension extends DataExtension {

	/**
	 * Max. file count in controlled upload folders.
	 * 
	 * @var int
	 */
	public static $folder_max_files = 100;

	/**
	 * Default folder name
	 * 
	 * @var string
	 */
	public static $default_folder_name = 'ControlledUploads';

	/**
	 * Stores class names and folder names fot setting up
	 * controlled upload folders.
	 * @var array
	 */
	private static $controlled_folders = array();

	/**
	 * Generates a folder config with default values.
	 * The defaults can be modified by given $config.
	 * To make this work $config should be an array with a pattern like
	 * array(
	 *	'folderName' => 'SomeName',
	 *	'folderMaxFiles' => 100
	 * );
	 * 
	 * @param  array $config
	 * @return array
	 */
	public static function get_folder_config($config = null) {
		$default = array(
			'folderName' => self::sanitize_folder_name(self::$default_folder_name),
			'folderMaxFiles' => self::$folder_max_files
		);
		if($config) {
			if(is_array($config)) {
				$config = array_merge($default, $config);
				//$config['folderMaxFiles'] = abs($config['folderMaxFiles']);
				$config['folderName'] = self::sanitize_folder_name($config['folderName']);
				return $config;
			} else {
				throw new InvalidArgumentException("config should be an array");
			}
		}
		return $default;
	} 
	/**
	 * Sets up a controlled upload folder [$folderName]
	 * for a class [$className].
	 * Limits the count of files on each folder to
	 * 
	 * @param  string $className
	 * @param  string|array $config
	 */
	public static function set_controlled_folder_for($className, $config) {
		if(is_array($className)){
			foreach($className as $modelclass){
				self::set_controlled_folder_for($modelclass,$config);
			}
		}else{
			Object::add_extension($className,'ControlledFolderDataExtension');
			if(is_string($config)) {
				$fconfig = self::get_folder_config();
				$fconfig['folderName'] = self::sanitize_folder_name($config);
			} else if(is_array($config)) {
				$fconfig = self::get_folder_config($config);
			}
			self::$controlled_folders[$className] = $fconfig;
		}
	}

	/**
	 * Limits the count of files in a folder to $folder_max_files.
	 * Automatically adds new subfolders.
	 * 
	 * @param string|array $config
	 * @return string
	 */
	public static function find_or_make_controlled_folder($config) {
		
		$foldername = self::sanitize_folder_name(self::$default_folder_name);
		$folderMaxFiles = self::$folder_max_files;

		if(is_string($config)) {
			$foldername = self::sanitize_folder_name($config);
		} else if(is_array($config)) {
			$config = self::get_folder_config($config);
			$foldername = self::sanitize_folder_name($config['folderName']);
			$folderMaxFiles = $config['folderMaxFiles'];
		}
		//  TEST VARS
		/*
		$foldername = "TheTestFolder2";
		$folderMaxFiles = 3;
		*/
		Folder::find_or_make($foldername);
		$folder = Controller::join_links(
			ASSETS_PATH,
			$foldername
		);


		$dir = opendir($folder);
		$lastfolder = '';
		$foldercount = 0;
		$subfolder = '';
		while ($file = readdir($dir)) {
			$currentDir = Controller::join_links(
				$folder,
				$file
			);
			if (substr($file,0, 1) != '_' 
				&& $file != '.' 
				&& $file != '..' 
				&& is_dir($currentDir)
			) {
				$filecount = count(glob($currentDir."/*.*"));
				$lastfolder = Controller::join_links(
					$folder,
					$file
				);
				$subfolder = $file;
				$foldercount++;
				if ($filecount < $folderMaxFiles) {
					break;
				}
			}
		}

		if ($lastfolder == '') {
			$newfolder = Folder::find_or_make(
				Controller::join_links(
					$foldername,
					'000000'
				)
			);
			return Controller::join_links(
				$foldername,
				$newfolder->Name
			);
		}

		$filecount = count(glob($lastfolder."/*.*"));
		if ($filecount < $folderMaxFiles 
			&& $subfolder != ''
		) {
			return Controller::join_links(
				$foldername,
				$subfolder
			);
		} else {
			$newfolder = Folder::find_or_make(
				Controller::join_links(
					$foldername,
					str_pad($foldercount, 6, "0", STR_PAD_LEFT)
				)
			);
			return Controller::join_links(
				$foldername,
				$newfolder->Name
			);
		}
	}

	/**
	 * Folder name sanitizer.
	 * Checks for valid names and sanitizes
	 * against directory traversal.
	 * 
	 * @param  string $foldername
	 * @return string
	 */
	public static function sanitize_folder_name($foldername) {
		//return $foldername;
		return FileNameFilter::create()->filter(basename($foldername));
	}
	/**
	 * Getter for the actual folder name.
	 * If an $config array is passed, it will return 
	 * a controlled folder with this configuration.
	 * It will also return controlled folders if they are setup by
	 * ControlledFolderDataExtension::set_controlled_folder_for.
	 * If $makePermanent is used, it will register this configuration
	 * for later use.
	 * 
	 * @param  array $config
	 * @param  array $makePermanent
	 * @return string
	 */
	public function getUploadFolder($config = null, $makePermanent = false) {
		$className = $this->owner->ClassName;
		if (is_array($config)) {
			if($makePermanent) {
				self::set_controlled_folder_for($className, $config);
			}
			return self::find_or_make_controlled_folder($config);
		} else if (array_key_exists($className, self::$controlled_folders)) {
			return self::find_or_make_controlled_folder(
				self::$controlled_folders[$className]
			);
		} else {
			$config = array(
				'folderName' => self::sanitize_folder_name($className),
				'folderMaxFiles' => self::$folder_max_files
			);
			self::set_controlled_folder_for(
				$className,
				$config
			);
			return self::find_or_make_controlled_folder($config);
		}
		if (isset($className::$upload_folder)) {
			return $className::$upload_folder;
		}
		return self::sanitize_folder_name(self::$default_folder_name);
	}
}