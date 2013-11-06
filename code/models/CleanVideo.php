<?php
/**
 * A DataObject for Videos
 * Provides a video that can either be embeded through
 * a service api or through its own embed code. 
 * 
 * @package cleanutilities
 * @subpackage models
 * 
 * @author arillo
 */
class CleanVideo extends DataObject {
	
	private static $db = array(
		"Title" => "Text",
		"VideoAddress" => "Text",
		"VideoType" => "Enum('Embed, File','Embed')",
		"Autoplay" => "Boolean"
	);
	
	private static $has_one = array(
		'Reference' => 'SiteTree',
		'VideoFile' => 'File'
	);
	
	/**
	 * Allowed file extensions for uploading.
	 * @var array
	 */
	public static $allowed_extensions = array('mov', 'flv', 'mp4');

	/**
	 * Specifies a custom upload folder name.
	 * @var string
	 */
	public static $upload_folder = 'Videos';


	public static $use_video_upload = true;
	/**
	 * Returns custom validator
	 * 
	 * @return CleanVideo_Validator
	 */
	public function getValidator() {
		return new CleanVideo_Validator();
	}
	
	/**
	 * Returns the actual video embed code.
	 * If $autoplay isset it will use this value
	 * instead of the value stored in DB.
	 * 
	 * @param int $width
	 * @param int $height
	 * @param boolean $autoplay
	 * @return string
	 */
	public function VideoEmbed($width = 640, $height = 375, $autoplay = null) {
		$autoplay = isset($autoplay) ? $autoplay : $this->Autoplay;
		switch($this->VideoType) {
			case 'Embed':
				if ($this->VideoAddress!='') {
					return VideoUtility::video_embed(
						$this->VideoAddress,
						$width,
						$height,
						$autoplay
					);
				}
				break;
			case 'File':
				if ($this->VideoFileID != 0 && isset($this->VideoFileID)) {
					$vars = array(
						'ID' => $this->ID,
						'Width' => $width,
						'Height' => $height,
						'VideoURL' => $this->VideoFile()->URL,
						'ThemeDir' => $this->ThemeDir(),
						'Autoplay' => $autoplay,
						'Module' => CleanUtils::$module
					);
					Requirements::javascript(CleanUtils::$module . "/javascript/libs/swfobject.js");
					Requirements::javascriptTemplate(CleanUtils::$module . "/javascript/init_video.js", $vars);
					return $this->customise($vars)->renderWith(array('VideoEmbed'));
				}
				break;
		}
		return false;
	}
	
	public function getCMSFields(){
		$fields = FieldList::create(
			new TabSet(
				"Root",
				new Tab("Main")
			)
		);
		$fields->addFieldToTab("Root.Main",LiteralField::create('VideoError','<div></div>'));
		$fields->addFieldToTab("Root.Main",CheckboxField::create(
			'Autoplay',
			_t('CleanVideo.AUTOPLAY', 'Auto play')
		));

		if(self::$use_video_upload){
			$fields->addFieldToTab("Root.Main",
				DropdownField::create(
					'VideoType',
					_t('CleanVideo.BEHAVIOUR', 'Choose a behaviour'),
					$this->dbObject('VideoType')->enumValues()
				)
			);
		}else{
			$fields->addFieldToTab("Root.Main",new HiddenField("VideoType","VideoType","Embed"));
		}
		$fields->addFieldToTab("Root.Main",TextField::create(
			'Title',
			_t('CleanUtilities.TITLE', 'Title')
		));
		$fields->addFieldToTab("Root.Main",TextField::create(
			'VideoAddress',
			_t('CleanVideo.VIDEO_URL', 'Video URL')
		));
		if(self::$use_video_upload){
			$fields->addFieldToTab("Root.Main",$upload = UploadField::create(
				'VideoFile',
				_t('CleanVideo.VIDEO_FILE', 'Video File')
			));
			$upload->getValidator()->setAllowedExtensions(self::$allowed_extensions);
			if($this->hasExtension('ControlledFolderDataExtension')) {
				$upload->setFolderName($this->getUploadFolder());
			} else {
				$upload->setFolderName(self::$upload_folder);
			}
		}
		$this->extend('updateCMSFields', $fields);
		return $fields;
	}
	
	/**
	 * Getter for the video file name
	 * 
	 * @return string
	 */
	public function getVideoFileName() {
		if($this->VideoFileID != 0 && isset($this->VideoFileID)) {
			return $this->VideoFile()->FileName;
		}
		return '';
	}
	
	/**
	 * Getter for the service's video id.
	 * 
	 * @return string
	 */
	public function VideoID() {
		$purl = VideoUtility::prepareURL($this->VideoAddress);
		return $purl['sourceid'];
	}
}

/**
 * Custom validator for the videos
 * 
 * @package cleanutilities
 * @subpackage models
 */
class CleanVideo_Validator extends RequiredFields {

	protected $customRequired = array('Number');

	public function __construct() {
		$required = func_get_args();
		if (isset($required[0]) && is_array($required[0])) $required = $required[0];
		$required = array_merge($required, $this->customRequired);
		parent::__construct($required);
	}

	function php($data){
		$valid = parent::php($data);
		if ($data['VideoType'] == 'Embed') {
			if ($data['VideoAddress'] != '') {
				if (VideoUtility::validate_video($data['VideoAddress']) == false) {
					$this->validationError(
						"VideoError",
						_t('Video_Validator.ADDRESS_ERROR','Please enter a valid Video URL')
					);
					$valid = false;
				}
			} else {
				$this->validationError(
					"VideoError",
					_t('Video_Validator.ADDRESS_REQUIRED','Video URL is required for Embeded videos')
				);
				$valid = false;
			}
		}
		if ($data['VideoType'] == 'File') {
			$videofile = $data['VideoFile'];
			if ($data['VideoFile'] == '') {
				$this->validationError(
					"VideoError",
					_t('Video_Validator.VIDEOFILE_REQUIRED','Video File is required for File videos')
				);
				$valid = false;
			}
		}
		return $valid;
	}
}