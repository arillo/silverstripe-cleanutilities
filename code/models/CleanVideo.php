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

	static $db = array(
		'Title' => 'Text',
		'VideoAddress' => 'Text',
		'VideoType' => "Enum('Embed, File','Embed')",
		'Autoplay' => 'Boolean'
	);

	static $has_one = array(
		'Reference' => 'SiteTree',
		'MP4File' => 'File',
		'OGVFile' => 'File',
		'WebMFile' => 'File',
		'PreviewImage' => 'Image'
	);
	
	/**
	 * Allowed file extensions for uploading.
	 * @var array
	 */
	public static $allowed_extensions = array('ogv', 'webm', 'mp4');

	/**
	 * Specifies a custom upload folder name.
	 * @var string
	 */
	public static $upload_folder = 'Videos';

	/**
	 * Enable video upload
	 * @var boolean
	 */
	public static $use_video_upload = true;

	/**
	 * Upload in MB
	 * @var integer
	 */
	public static $upload_limit = 25;

	/**
	 * Upload in MB
	 * @var integer
	 */
	public static $default_css = true;

	/**
	 * Make sure that js injected once only
	 * @var boolean
	 */
	protected static $js_initialized = false;

	/**
	 * Inject required js
	 */
	public static function init_js() {
		if (!self::$js_initialized) {
			if (self::$default_css) {
				Requirements::css(CleanUtils::$module . '/css/video-js.css');
			}
			Requirements::javascript(CleanUtils::$module . '/javascript/libs/video.js');
			
			$swf = Controller::join_links(
				Director::absoluteBaseURL(),
				CleanUtils::$module,
				'swf/video-js.swf'
			);
			Requirements::customScript("
				;(function($){
					$(document).ready(function(){
						videojs.options.flash.swf = '$swf';
					});
				})(jQuery);
			");
			self::$js_initialized = true;
		}
	}

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
				if ($this->MP4File()->exists()
					&& $this->OGVFile()->exists()
					&& $this->WebMFile()->exists()
				) {
					self::init_js();
					$vars = array(
						'ID' => $this->ID,
						'Width' => $width,
						'Height' => $height,
						'PreviewImage' => $this->PreviewImage(),
						'MP4File' => $this->MP4File(),
						'OGVFile' => $this->OGVFile(),
						'WebMFile' => $this->WebMFile(),
						'Autoplay' => $autoplay
					);
					return $this->customise($vars)->renderWith(array('VideoEmbed'));
				}
				break;
		}
		return false;
	}

	public function getCMSFields(){
		$fields = FieldList::create(
			new TabSet(
				'Root',
				new Tab('Main')
			)
		);

		$fields->addFieldToTab(
			'Root.Main',
			LiteralField::create('VideoError', '<div></div>')
		);
		$fields->addFieldToTab(
			'Root.Main',
			CheckboxField::create(
				'Autoplay',
				_t('CleanVideo.AUTOPLAY', 'Auto play')
			)
		);

		if (self::$use_video_upload) {
			$fields->addFieldToTab(
				'Root.Main',
				$videoType = OptionSetField::create(
					'VideoType',
					_t('CleanVideo.BEHAVIOUR', 'Choose a behaviour'),
					$this->dbObject('VideoType')->enumValues()
				)
			);
		} else {
			$fields->addFieldToTab(
				'Root.Main',
				new HiddenField('VideoTypes', 'VideoType', 'Embed'));
		}

		$fields->addFieldToTab(
			'Root.Main',
			TextField::create(
				'Title',
				_t('CleanUtilities.TITLE', 'Title')
			)
		);

		$fields->addFieldToTab(
			'Root.Main',
			$url = TextField::create(
				'VideoAddress',
				_t('CleanVideo.VIDEO_URL', 'Video URL')
			)
		);
		$url->displayIf('VideoType')->isEqualTo('Embed');

		if (self::$use_video_upload && $this->ID) {
			$uploadLimit = self::$upload_limit * 1024 * 1024;
			$uploadFolder = self::$upload_folder;
			if($this->hasExtension('ControlledFolderDataExtension')) {
				$uploadFolder = $this->getUploadFolder();
			}
			$uploadFolder = Controller::join_links(
				$uploadFolder,
				$this->ID
			);

			// preview image
			$fields->addFieldToTab(
				'Root.Main',
				$previewImage = CleanUtils::create_uploadfield_for(
					'PreviewImage',
					_t('CleanVideo.PREVIEW_IAMGE', 'Preview image'),
					$this,
					CleanImage::$allowed_extensions,
					$uploadFolder
				)
			);
			$previewImage->getValidator()->setAllowedMaxFileSize($uploadLimit);
			$previewImage->hideIf('VideoType')->isEqualTo('Embed');

			// mp4
			$fields->addFieldToTab(
				'Root.Main',
				$mp4 = CleanUtils::create_uploadfield_for(
					'MP4File',
					_t('CleanVideo.MP4_FILE', 'Mp4 File'),
					$this,
					array('mp4'),
					$uploadFolder
				)
			);
			$mp4->getValidator()->setAllowedMaxFileSize($uploadLimit);
			$mp4->hideIf('VideoType')->isEqualTo('Embed');

			// ogv
			$fields->addFieldToTab(
				'Root.Main',
				$ogv = CleanUtils::create_uploadfield_for(
					'OGVFile',
					_t('CleanVideo.OGV_FILE', 'Ogv File'),
					$this,
					array('ogv'),
					$uploadFolder
				)
			);
			$ogv->getValidator()->setAllowedMaxFileSize($uploadLimit);
			$ogv->hideUnless('VideoType')->isEqualTo('File');

			// webm
			$fields->addFieldToTab(
				'Root.Main',
				$webm = CleanUtils::create_uploadfield_for(
					'WebMFile',
					_t('CleanVideo.WEBM_FILE', 'Webm File'),
					$this,
					array('webm'),
					$uploadFolder
				)->hideUnless('VideoType')->isEqualTo('File')->end()
			);
			$webm->getValidator()->setAllowedMaxFileSize($uploadLimit);
		}

		$fileUploads = HeaderField::create(
			'UploadInfo',
			_t('CleanVideo.UPLOAD_INFO', 'INFO: Uploads can be attached after first saving.'),
			4
		);

		$this->extend('updateCMSFields', $fields);
		return $fields;
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