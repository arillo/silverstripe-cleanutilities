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
		'ExternalLink' => 'Varchar(255)',
		'Target' => "Enum('_blank, _self', '_blank')"
	);

	static $has_one = array(
		'Reference' => 'SiteTree',
		'InternalLink' => 'SiteTree'
	);

	static $searchable_fields = array(
		'Title',
		'ExternalLink',
		'Reference.Title'
	);
	
	static $summary_fields = array(
		'Title' => 'Title',
		'Link' => 'Link',
		'Target' => 'Target'
	);

	public function getCMSFields() {
		$fields = FieldList::create(
			new TabSet(
				'Root',
				new Tab('Main')
			)
		);

		$fields->addFieldToTab(
			'Root.Main',
			$linkType = OptionsetField::create(
				'LinkType',
				_t('CleanLink.LINK_TYPE', 'link type'),
				array(
					'internal' => _t('CleanLink.INTERNAL_LINK', 'Internal link'),
					'external' => _t('CleanLink.EXTERNAL_LINK', 'External link')
				)
			)
		);

		if (!$this->LinkType) {
			$linkType->setValue('internal');
		}
		$fields->addFieldToTab(
			'Root.Main',
			TextField::create(
				'Title',
				_t('CleanUtilities.Title', 'Title')
			)
		);

		$fields->addFieldToTab(
			'Root.Main',
			DropdownField::create(
				'Target',
				_t('CleanLink.TARGET', 'Choose the target'),
				$this->dbObject('Target')->enumValues()
			)
		);

		$fields->addFieldToTab(
			'Root.Main',
			TextField::create(
				'ExternalLink',
				_t('CleanLink.EXTERNAL_LINK', 'External link')
			)
				->hideIf('LinkType')->isEqualTo('internal')
				->orIf('InternalLinkID')->isGreaterThan(0)
				->end()
		);

		$fields->addFieldToTab(
			'Root.Main',
			DropdownField::create(
				'InternalLinkID',
				_t('CleanLink.INTERNAL_LINK', 'Internal link'),
				SiteTree::get()->map()
			)
				->setEmptyString(_t('CleanLink.EMPTY_STRING', 'Please choose'))
				->hideIf('LinkType')->isEqualTo('external')
				->orIf('ExternalLink')->isNotEmpty()
				->end()
		);

		$this->extend('updateCMSFields', $fields);
		return $fields;
	}

	public function getLink() {
		if ($this->ExternalLink) {
			return $this->ExternalLink;
		}
		$page = $this->InternalLink();
		if ($page->exists()) {
			return $this->InternalLink()->Link();
		}
		return _t('CleanLink.NO_LINK', 'No link set.');
	}
}