<?php
/**
 * A DataObject for Videos
 * Provides a video that can either be embeded through
 * a service api or file uploads.
 * It uses video.js, visit:
 * http://www.videojs.com/
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
		'VideoType' => "Enum('Embed, File', 'Embed')",
		'Autoplay' => 'Boolean',
		'Controls' => 'Boolean',
		'Loop' => 'Boolean',
		'Preload' => "Enum('auto, none, metadata', 'auto')"
	);

	static $has_one = array(
		'Reference' => 'SiteTree',
		'MP4File' => 'File',
		'OGVFile' => 'File',
		'WebMFile' => 'File',
		'PreviewImage' => 'Image'
	);

	private static $defaults = array(
		'Autoplay' => false,
		'Controls' => true,
		'Loop' => false
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

	protected static $config = array(
		'load_default_css' => true,
		'load_modernizr' => true
	);

	/**
	 * Make sure that js is injected once only
	 * @var boolean
	 */
	protected static $js_initialized = false;

	/**
	 * Inject required js.
	 * $config can override values in {@see self::$config}.
	 * 
	 * @param  array $config
	 */
	public static function init_js($config = array()) {
		if (!self::$js_initialized) {
			if (is_array($config) && count($config) > 0) {
				self::$config = array_merge(self::$config, $config);
			}
			if (self::get_setting('load_default_css')) {
				Requirements::css(CleanUtils::$module . '/css/video-js.css');
			}
			if (self::get_setting('load_modernizr')) {
				Requirements::javascript(CleanUtils::$module . '/javascript/libs/modernizr.custom.js');
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
	 * Setter for configuration
	 * 
	 * @param string $key
	 * @param $value
	 */
	public static function set_config($key, $value = null) {
		if (is_array($key)) {
			self::$config = array_merge(self::$config, $key);
		} else if ($key && $value) {
			self::$config[$key] = $value;
		}
	}

	/**
	 * Getter for a setting
	 * 
	 * @param  string $key
	 * @return mixed
	 */
	public static function get_setting($key) {
		if (isset(self::$config[$key])) {
			return self::$config[$key];
		}
		return false;
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
	 * Some parameters can be set via template.
	 * These will override the according db values.
	 * 
	 * @param int $width
	 * @param int $height
	 * @param boolean $autoplay
	 * @param boolean $controls
	 * @param boolean $loop
	 * @param string $preload  (auto, none, metadata)
	 * @return string
	 */
	public function VideoEmbed($width = 640, $height = 375, $autoplay = null, $controls = null, $loop = null, $preload = null) {
		$autoplay = isset($autoplay) && is_bool(filter_var($autoplay, FILTER_VALIDATE_BOOLEAN)) ? $autoplay : $this->Autoplay;

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
					$controls = isset($controls) && is_bool(filter_var($controls, FILTER_VALIDATE_BOOLEAN)) ? $controls : $this->Controls;
					$loop = isset($loop) && is_bool(filter_var($loop, FILTER_VALIDATE_BOOLEAN)) ? $loop : $this->Loop;
					$preload = isset($preload) && array_search($preload, $this->obj('Preload')->enumValues()) ? $preload : $this->Preload;
					$vars = array(
						'ID' => $this->ID,
						'Width' => $width,
						'Height' => $height,
						'PreviewImage' => $this->PreviewImage(),
						'MP4File' => $this->MP4File(),
						'OGVFile' => $this->OGVFile(),
						'WebMFile' => $this->WebMFile(),
						'Setup' => '{ "controls": ' . $controls . ', "autoplay":' . $autoplay . ', "preload": "' . $preload . ', "loop" : "' . $loop . '" }'
					);
					return $this->customise($vars)->renderWith(array('VideoJSEmbed'));
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

		if (self::$use_video_upload) {
			$fields->addFieldToTab(
				'Root.Main',
				$videoType = OptionSetField::create(
					'VideoType',
					_t('CleanVideo.BEHAVIOUR', 'Choose a behaviour'),
					$this->dbObject('VideoType')->enumValues()
				)
			);
			if (!$this->VideoType) {
				$videoType->setValue('Embed');
			}
		} else {
			$fields->addFieldToTab(
				'Root.Main',
				new HiddenField('VideoType', 'VideoType', 'Embed'));
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
				)
			);
			$webm->getValidator()->setAllowedMaxFileSize($uploadLimit);
			$webm->hideUnless('VideoType')->isEqualTo('File');

			$fields->addFieldToTab(
				'Root.Main',
				$controls = CheckboxField::create(
					'Controls',
					_t('CleanVideo.CONTROLS', 'Show controls')
				)
			);
			$controls->hideUnless('VideoType')->isEqualTo('File');

			$fields->addFieldToTab(
				'Root.Main',
				$loop = CheckboxField::create(
					'Loop',
					_t('CleanVideo.LOOP', 'Loop video')
				)
			);
			$loop->hideUnless('VideoType')->isEqualTo('File');

			$fields->addFieldToTab(
				'Root.Main',
				$preload = DropdownField::create(
					'Preload',
					_t('CleanVideo.PRELOAD', 'Preload video'),
					$this->obj('Preload')->enumValues()
				)
			);
			$preload->hideUnless('VideoType')->isEqualTo('File');
		}

		// show info if we have no ID.
		// using TextField instead of HeaderField here, because display-logic doesn't work with it.
		if (!$this->ID) {
			$fields->addFieldToTab(
				'Root.Main',
				$videoUploadInfo = TextField::create(
					'VideoUploadInfo',
					'Info',
					_t('CleanVideo.UPLOAD_INFO', 'Uploads can be attached after first saving.')
				)
			);
			$videoUploadInfo->performReadonlyTransformation();
			$videoUploadInfo->setDisabled(true);
			$videoUploadInfo->hideUnless('VideoType')->isEqualTo('File');
		}

		$fields->addFieldToTab(
			'Root.Main',
			CheckboxField::create(
				'Autoplay',
				_t('CleanVideo.AUTOPLAY', 'Auto play')
			)
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
		$purl = VideoUtility::prepare_url($this->VideoAddress);
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
						'VideoError',
						_t('Video_Validator.ADDRESS_ERROR', 'Please enter a valid Video URL')
					);
					$valid = false;
				}
			} else {
				$this->validationError(
					'VideoError',
					_t('Video_Validator.ADDRESS_REQUIRED', 'Video URL is required for Embeded videos')
				);
				$valid = false;
			}
		}
		if ($data['VideoType'] == 'File') {
			if ($data['MP4File'] == ''
				|| $data['OGVFile'] == ''
				|| $data['WebMFile'] == ''
			) {
				$this->validationError(
					'VideoError',
					_t('Video_Validator.VIDEOFILES_REQUIRED', 'It looks like some video files are missing.')
				);
				$valid = false;
			}
		}
		return $valid;
	}
}