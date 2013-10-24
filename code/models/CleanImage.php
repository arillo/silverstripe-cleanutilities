<?php 
/**
 * A wrapper for File, which adds a Title field
 * and a relation to it's page.
 * 
 * @package cleanutilities
 * @subpackage models
 * 
 * @author arillo
 */
class CleanImage extends DataObject {
	
	static $db = array(
		'Title'=> 'Text'
	);
	
	static $has_one = array(
		'Attachment' => 'Image',
		'Reference' => 'SiteTree'
	);
	
	static $searchable_fields = array(
		'Title',
		'Attachment.Title'
	);

	static $summary_fields = array(
		'Thumbnail' => 'Image',
		'Title' => 'Title'
	);

		/**
	 * This var specifies the name of the upload folder
	 * @var string
	 */
	public static $upload_folder = "Images";

	/**
	 * Allowed file extensions for uploading.
	 * @var array
	 */
	public static $allowed_extensions = array(
		'', 'bmp','png','gif','jpg','jpeg','ico','pcx','tif','tiff'
		
	);
	
	/**
	 * Getter for current upload folder.
	 * @return string
	 */
	public static function get_upload_folder() {
		return self::$use_controlled_upload_folder ?
				CleanUtils::controlled_upload_folder(self::$upload_folder) :
				self::$upload_folder;
	}

	public function getCMSFields(){
		$fields = FieldList::create(
			new TabSet(
				"Root",
				new Tab("Main")
			)
		);
		$fields->addFieldToTab(
			'Root.Main',
			TextField::create('Title',
				_t('CleanUtilities.TITLE', 'Title')
			)
		);
		$upload = UploadField::create('Attachment', 'Image');
		$upload->setConfig('allowedMaxFileNumber', 1);
		$upload->getValidator()->setAllowedExtensions(
			self::$allowed_extensions
		);
		if($this->hasExtension('ControlledFolderDataExtension')) {
			$upload->setFolderName($this->getUploadFolder());
		} else {
			$upload->setFolderName(self::$upload_folder);
		}
		$fields->addFieldToTab(
			'Root.Main',
			$upload
		);
		$this->extend('updateCMSFields', $fields);
		return $fields;
	}
	
	/**
	 * Returns CMS thumbnail, if an image is attached.
	 * Mainly used by GridField.
	 *
	 * @return mixed
	 */
	public function getThumbnail() {
		if ($image = $this->Attachment()) {
			return $image->CMSThumbnail();
		}
		return _t('CleanImage.NO_IMAGE', '(No Image)');
	}
	
	/**
	 * Returns a download link like:
	 * URLSegment/download/ClassName/ID
	 * 
	 * To make this to work you need to implement a "download" function in
	 * the Reference's controller.
	 * This can be achieved by using DownloadExtension.
	 * 
	 * @return string
	 */
	public function DownloadLink() {
		return Controller::join_links(
			$this->Reference()->Link(),
			'download',
			$this->ClassName,
			$this->ID
		);
	}

	/**
	 * Returns a absolute download link like:
	 * http://domain.com/URLSegment/download/ClassName/ID
	 * 
	 * To make this to work you need to implement a "download" function in
	 * the Reference's controller.
	 * This can be achieved by using DownloadExtension.
	 * 
	 * @return string
	 */
	public function AbsoluteDownloadLink() {
		return Director::absoluteURL(
			$this->DownloadLink()
		);
	}
}