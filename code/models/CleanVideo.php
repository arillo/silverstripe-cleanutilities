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
class CleanVideo extends DataObject{
	static $db = array(
		"Title" => "Text",
		"VideoAddress" => "Text",
		"VideoType" => "Enum('Embed, File','Embed')",
		"Autoplay" => "Boolean"
	);
	static $has_one = array(
		'Reference' => 'SiteTree',
		'VideoFile' => 'File'
	);
	/**
	* Specifies a custom upload folder name.
	* @var string
	*/
	static $upload_folder;
	public static $use_video_upload = true;

	/**
	 * Returns custom validator
	 *
	 * @return CleanVideo_Validator
	 */
	public function getValidator(){ return new CleanVideo_Validator(); }

	/**
	 * Returns the actual video embed code.
	 *
	 * @param int $width
	 * @param int $height
	 * @return string
	 */
	public function VideoEmbed($width = 640, $height = 375){
		switch($this->VideoType){
			case 'Embed':
				if($this->VideoAddress!=''){
					return VideoUtility::video_embed($this->VideoAddress,$width,$height,$this->Autoplay);
				}
				break;
			case 'File':
				if($this->VideoFileID != 0 && isset($this->VideoFileID)){
					$vars = array(
						'ID' => $this->ID,
						'Width' => $width,
						'Height' => $height,
						'VideoURL' => $this->VideoFile()->URL,
						'ThemeDir' => $this->ThemeDir(),
						'Autoplay' => $this->Autoplay,
						'Module' => CleanUtils::$module
					);
					Requirements::javascript(CleanUtils::$module . "/javascript/libs/swfobject.js");
					Requirements::javascriptTemplate(CleanUtils::$module . "/javascript/init_video.js", $vars);

					$videoembed = $this->customise($vars)->renderWith(array('VideoEmbed'));

					return $videoembed;
				}
				break;
		}
		return false;
	}

	/**
	 * Returns the actual video embed code.
	 * Allows to set its auto-play to on/off.
	 *
	 * @param int $width
	 * @param int $height
	 * @return string
	 */
	public function VideoEmbedAuto($width = 640, $height = 375, $autoplay = false){
		switch($this->VideoType){
			case 'Embed':
				if($this->VideoAddress!=''){
					return VideoUtility::video_embed($this->VideoAddress,$width,$height,$autoplay);
				}
				break;
			case 'File':
				if($this->VideoFileID != 0 && isset($this->VideoFileID)){
					$vars = array(
						'ID' => $this->ID,
						'Width' => $width,
						'Height' => $height,
						'VideoURL' => $this->VideoFile()->URL,
						'ThemeDir' => $this->ThemeDir(),
						'Autoplay' => $this->Autoplay
					);
					Requirements::javascript("cleanutilities/javascript/libs/swfobject.js");
					Requirements::javascriptTemplate("cleanutilities/javascript/init_video.js",$vars);
					$videoembed = $this->customise($vars)->renderWith(array('VideoEmbed'));
					return $videoembed;
				}
				break;
		}
		return false;
	}
	/**
	 * Form fields for use with DataObjectManager
	 *
	 * @return FieldSet
	 */
	public function getCMSFields_forPopup(){
		$fields = new FieldSet();
		$fields = new FieldSet(new Tabset('Root',new Tab('Main')));
		$fields->addFieldToTab("Root.Main",new LiteralField('VideoError','<div></div>'));
		$fields->addFieldToTab("Root.Main",new CheckboxField('Autoplay','Autoplay'));
		if(self::$use_video_upload){
			$fields->addFieldToTab("Root.Main",
				new DropdownField(
					'VideoType',
					'Choose a behaviour',
					$this->dbObject('VideoType')->enumValues()
				)
			);
		}else{
			$fields->addFieldToTab("Root.Main",new HiddenField("VideoType","VideoType","Embed"));
		}
		$fields->addFieldToTab("Root.Main",new TextField('Title','Title'));
		$fields->addFieldToTab("Root.Main",new TextField('VideoAddress','Video URL'));
		if(self::$use_video_upload){
			$fields->addFieldToTab("Root.Main",$up = new FileUploadField('VideoFile','Video File'));
			$destination = isset(self::$upload_folder) ? self::$upload_folder : 'Videos';
			$up->setUploadFolder($destination);
			$up-> setFileTypes(array('mov','flv','mp4'));
		}
		$this->extend('updateCMSFields_forPopup', $fields);
		return $fields;
	}
	/**
	 * Getter for the video file name
	 *
	 * @return string
	 */
	public function getVideoFileName(){
		if($this->VideoFileID != 0 && isset($this->VideoFileID)) return $this->VideoFile()->FileName;

		return '';
	}

	/**
	 * Getter for the service's video id.
	 *
	 * @return string
	 */
	public function VideoID(){
		$purl = VideoUtility::prepare_url($this->VideoAddress);
		return $purl['sourceid'];
	}
	public function onBeforeWrite(){
		parent::onBeforeWrite();
		if(!self::$use_video_upload){
			$this->VideoType = "Embed";
		}
	}
	/**
	* Permissions
	*/
	public function canView($member = null) {
		return true;
	}
	public function canEdit($member = null) {
		return true;
	}
	public function canDelete($member = null) {
		return true;
	}
	public function canCreate($member = null) {
		return true;
	}
}

/**
 * Custom validator for the videos
 *
 * @package cleanutilities
 * @subpackage models
 */
class CleanVideo_Validator extends RequiredFields{

	protected $customRequired = array('Number');

	public function __construct() {
		$required = func_get_args();
		if(isset($required[0]) && is_array($required[0])) $required = $required[0];
		$required = array_merge($required, $this->customRequired);
		parent::__construct($required);
	}


	function php($data){
		$valid = parent::php($data);
		if($data['VideoType'] == 'Embed'){
			if($data['VideoAddress'] != ''){
				if(VideoUtility::validate_video($data['VideoAddress']) == false){
					Debug::show('we have an error');
					$this->validationError("VideoError", _t('Video_Validator.ADDRESS_ERROR','Please enter a valid Video URL'));
					$valid = false;
				}
			}else{
				$this->validationError("VideoError", _t('Video_Validator.ADDRESS_REQUIRED','Video URL is required for Embeded videos'));
				$valid = false;
			}
		}
		if($data['VideoType'] == 'File'){
			$videofile = $data['VideoFile'];
			if($data['VideoFile'] == ''){
				$this->validationError("VideoError", _t('Video_Validator.VIDEOFILE_REQUIRED','Video File is required for File videos'));
				$valid = false;
			}
		}
		return $valid;
	}
}