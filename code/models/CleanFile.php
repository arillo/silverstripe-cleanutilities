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
class CleanFile extends DataObject{

	static $db = array (
		'Title'=> 'Text'
	);
	static $has_one = array (
		'Attachment' => 'File',
		'Reference' => 'SiteTree'
	);
	static $searchable_fields = array(
		'Title',
		'Reference.Title'
	);
	static $summary_fields = array(
		'Title',
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
		$upload = new FileUploadField('Attachment');
		$destination = isset(self::$upload_folder) ? self::$upload_folder : '/files/';
		$upload->setUploadFolder($this->ControlledUploadFolder($destination));
		$fields = new FieldSet(
			new Tabset('Root',
				new Tab('Main',
					new TextField('Title', 'Title'),
					$upload
				)
			)
		);
		$this->extend('updateCMSFields_forPopup', $fields);
		return $fields;
	}
	/**
	 * Returns a link like URLSegment/download/ClassName/ID.
	 * To make this to work you need to implement a "download" function in
	 * the Reference' s controller.
	 *
	 * @return mixed
	 */
	public function DownloadLink(){
		if($this->ReferenceID != 0){
			return Controller::join_links(
				$this->Reference()->Link(),
				'download',
				$this->ClassName,
				$this->ID
			);
		}
		return false;
	}
}