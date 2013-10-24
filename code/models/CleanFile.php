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
class CleanFile extends DataObject {
	
	static $db = array(
		'Title'=> 'Text'
	);
	
	static $has_one = array(
		'Attachment' => 'File',
		'Reference' => 'SiteTree'
	);
	
	static $searchable_fields = array(
		'Title',
		"Attachment.Extension",
		'Attachment.Title'
	);

	static $summary_fields = array(
		'Title' => 'Title',
		'Attachment.Extension' => 'Type',
		'Attachment.Title' => 'Attachment'
	);

	/**
	 * Allowed file extensions for uploading.
	 * @var array
	 */
	public static $allowed_extensions = array(
		'','html','htm','xhtml','js','css',
		'bmp','png','gif','jpg','jpeg','ico','pcx','tif','tiff',
		'au','mid','midi','mpa','mp3','ogg','m4a','ra','wma','wav','cda',
		'avi','mpg','mpeg','asf','wmv','m4v','mov','mkv','mp4','swf','flv','ram','rm',
		'doc','docx','txt','rtf','xls','xlsx','pages',
		'ppt','pptx','pps','csv',
		'cab','arj','tar','zip','zipx','sit','sitx','gz','tgz','bz2','ace','arc','pkg','dmg','hqx','jar',
		'xml','pdf',
	);
	/**
	 * This var specifies the name of the upload folder
	 * @var string
	 */
	public static $upload_folder = "Files";
	
	/**
	 * CMS fields, can be extended by write your
	 * own updateCMSFields function.
	 * @return FieldList
	 */
	public function getCMSFields() {
		$fields = FieldList::create(
			new TabSet(
				"Root",
				new Tab("Main")
			)
		);
		$fields->addFieldToTab(
			'Root.Main',
			TextField::create(
				'Title',
				_t('CleanUtilities.TITLE', 'Title')
			)
		);
		$upload = UploadField::create(
			'Attachment',
			_t('CleanFile.FILE', 'File')
		);
		$upload->setConfig('allowedMaxFileNumber', 1);
		$upload->getValidator()->setAllowedExtensions(self::$allowed_extensions);
		if($this->hasExtension('ControlledFolderDataExtension')) {
			$upload->setFolderName($this->getUploadFolder());
		} else {
			$upload->setFolderName(self::$upload_folder);
		}
		$fields->addFieldToTab('Root.Main',$upload);
		$this->extend('updateCMSFields', $fields);
		return $fields;
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