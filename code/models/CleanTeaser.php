<?php
/**
 * A DataObject for Teasers with Title and Description
 *
 * @package cleanutilities
 * @subpackage models
 *
 * @author arillo
 */
class CleanTeaser extends DataObject {

	static $db = array(
		'Title'=> 'Text',
		'Description' => 'HTMLText'
	);
	
	static $has_one = array(
		'Reference' => 'SiteTree'
	);
	
	static $searchable_fields = array(
		'Title',
		'Reference.Title'
	);

	static $summary_fields = array(
		'Title',
		'Description' => 'Description'
	);

	public static $upload_folder = 'Teaser';

	public static $singular_name = 'Teaser';
	public static $plural_name = 'Teasers';

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->removeByName('ReferenceID');
		$this->extend('updateCMSFields', $fields);
		return $fields;
	}

	/**
	 * Summarized description for GridField
	 * 
	 * @return [type] [description]
	 */
	public function getCleanDescription() {
		return TextDataExtension::summarize($this->Description, 200);
	}
}