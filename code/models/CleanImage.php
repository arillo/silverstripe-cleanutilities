<?php
/**
 * A wrapper for Image, which adds a Title field
 * and a relation to it's page.
 *
 * @package cleanutilities
 * @subpackage models
 *
 * @author arillo
 */
class CleanImage extends DataObject{

	static $db = array (
		'Title'=> 'Text'
	);
	static $has_one = array (
		'Attachment' => 'Image',
		'Reference' => 'SiteTree'
	);
	static $searchable_fields = array(
		'Reference.Title'
	);
	static $summary_fields = array(
		'Reference.Title'
	);

	/**
	* Specifies a custom upload folder name.
	* @var string
	*/
	static $upload_folder;

	/**
	* Prepares this model for usage with DataObjectManager.
	* @return FieldSet
	*/
	public function getCMSFields_forPopup(){
		$upload = new ImageUploadField('Attachment');
		$destination = isset(self::$upload_folder) ? self::$upload_folder : '/images/';
		$upload->setUploadFolder($this->ControlledUploadFolder($destination));
		$fields = new FieldSet(
			new TextField('Title','Title'),
			$upload
		);
		$this->extend('updateCMSFields_forPopup', $fields);
		return $fields;
	}

	/**
	 * Returns CMS thumbnail, if an image is attached.
	 * Mainly used by DataObjectManager.
	 *
	 * @return mixed
	 */
	function getThumbnail(){
		if ($image = $this->Attachment()){
			return $image->CMSThumbnail();
		}
		return _t('CleanImage.NO_IMAGE', '(No Image)');
	}

	/**
	 * Returns a relative link like URLSegment/download/ClassName/ID.
	 * To make this to work you need to implement a "download" function in
	 * the Reference' s controller.
	 *
	 * @return string
	 */
	public function DownloadLink(){
		return Controller::join_links(
			$this->Reference()->Link(),
			'download',
			$this->ClassName,
			$this->ID
		);
	}

	/**
	 * Returns an absolute link
	 *
	 * @return mixed
	 */
	public function AbsoluteLink(){
		if($this->owner->ReferenceID != 0 && isset($this->owner->ReferenceID)){
			return Controller::join_links(
				$this->Reference()->AbsoluteLink(),
				$this->ClassName,
				$this->ID
			);
		}
		return false;
	}

	/**
	 * Returns an relative link
	 *
	 * @return mixed
	 */
	public function Link(){
		if($this->ReferenceID != 0 && isset($this->ReferenceID)){
			return Controller::join_links(
				$this->Reference()->Link(),
				$this->ClassName,
				$this->ID
			);
		}
		return false;
	}
}