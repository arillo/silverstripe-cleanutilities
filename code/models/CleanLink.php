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
	
	static $db = array(
		'Title' => 'Text',
		'URL' => 'Varchar(255)',
		'Target' => "Enum('_blank,_self','_blank')"
	);

	static $has_one = array(
		'Reference' => 'SiteTree'
	);
	
	static $searchable_fields = array(
		'Title',
		'URL',
		'Reference.Title'
	);
	
	static $summary_fields = array(
		'Title' => 'Title',
		'URL' => 'URL',
		'Target' => 'Target'
	);

	public function getCMSFields() {
		$options = DropdownField::create(
			'Target',
			_t('CleanLink.TARGET', 'Choose the target'),
			$this->dbObject('Target')->enumValues()
		);
		$fields = FieldList::create(
			new TabSet(
				"Root",
				new Tab("Main",
					TextField::create(
						'Title',
						_t('CleanUtilities.Title', 'Title')
					),
					TextField::create(
						'URL',
						_t('CleanUtilities.URL', 'Url')
					),
					$options
				)
			)
		);
		$this->extend('updateCMSFields', $fields);
		return $fields;
	}
}