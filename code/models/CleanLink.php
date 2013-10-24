<?php
/**
 * A DataObject for Links
 * 
 * @package cleanutilities
 * @subpackage models
 * 
 * @author arillo
 */
class CleanLink extends DataObject{
	
	static $db = array (
		'Title' => 'Text',
		'URL' => 'Varchar(255)',
		'Target'	=> "Enum('_self,_blank','_self')"
	);
	
	static $has_one = array (
		'Reference' => 'SiteTree'
	);
	
	static $searchable_fields = array(
		'Title',
		'URL',
		'Reference.Title'
	);
	
	static $summary_fields = array(
		'Title',
		'URL',
		'Reference.Title'
	);
	
	public function getCMSFields_forPopup(){
		$options = new DropdownField(
			'Target',
			'Choose the target',
			$this->dbObject('Target')->enumValues()
		);
		$fields = new FieldSet(
			new Tabset('Root',
				new Tab('Main',
					new TextField('Title','Title'),
					new TextField('URL','URL'),
					$options
				)
			)
		);
		$this->extend('updateCMSFields_forPopup', $fields);
		return $fields;
	}
}