<?php
/**
 * A DataObject for Links in CleanTeasers
 *
 * @package cleanutilities
 * @subpackage models
 *
 * @author arillo
 */
class CleanTeaserLink extends DataObject {
	static $db = array(
		'Title'=> 'Text',
		'URL' => 'Text',
		'Target' => "Enum('_blank,_self','_blank')"
	);
	static $has_one = array(
		'Reference' => 'CleanTeaser'
	);
	static $searchable_fields = array(
		'Title',
		'URL',
		'Reference.Title'
	);
	static $summary_fields = array(
		'Title',
		'URL',
		'Target'
	);
	public function getCMSFields() {
		$fields = FieldList::create(
			TextField::create(
				'Title',
				_t('CleanUtilities.TITLE', 'Title')
			),
			TextField::create(
				'URL',
				_t('CleanUtilities.URL', 'Url')
			),
			DropdownField::create(
				'Target',
				_t('CleanUtilities.TARGET', 'Link target'),
				$this->dbObject('Target')->enumValues()
			)
		);
		$this->extend('updateCMSFields', $fields);
		return $fields;
	}
}