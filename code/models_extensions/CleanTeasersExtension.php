<?php
/**
 * Provides your SiteTree class with has_many teasers feature.
 * It will utilize CleanTeaser 's.
 * 
 * Add this extension to a SiteTree instance
 * by adding this to your _config.php:
 * 
 * Object::add_extension('Page', 'CleanTeasersExtension');
 * 
 * @package cleanutilities
 * @subpackage models_extensions
 * 
 * @author arillo
 */
class CleanTeasersExtension extends DataExtension {

	static $has_many = array(
		'CleanTeasers' => 'CleanTeaser'
	);
	
	function updateCMSFields(FieldList $fields) {
		$sortable = singleton('CleanTeaser')->hasExtension('SortableDataExtension');
		$config = GridFieldConfig_RelationEditor::create();
		$config->addComponent($gridFieldForm = new GridFieldDetailForm());

		$dataFields = array();
		if (singleton('CleanTeaser')->hasExtension('CMSPublishableDataExtension')) {
			$dataFields['PublishIndicator'] = 'Published';
		}
		$dataFields = array_merge($dataFields, array(
			'Title' => 'Title',
			'CleanDescription' => 'Description'
		));
		$config->getComponentByType('GridFieldDataColumns')->setDisplayFields($dataFields); 
		$gridFieldForm->setTemplate('CMSGridFieldPopupForms');

		if ($sortable) {
			$config->addComponent(new GridFieldSortableRows('SortOrder'));
		}

		if ($sortable) {
			$data = $this->owner->CleanTeasers("ClassName = 'CleanTeaser'")->sort('SortOrder');
		} else {
			$data = $this->owner->CleanTeasers("ClassName = 'CleanTeaser'");
		}

		$fields->addFieldToTab(
			'Root.Teasers',
			GridField::create('CleanTeasers', 'Teaser', $data, $config)
		);
	}

	/**
	 * Getter for the attached teasers.
	 * You can specifiy a range of those links.
	 * 
	 * @param int $limit
	 * @param int $offset
	 * @param string $sortField
	 * @param string $sortDir
	 */
	public function Teasers($limit = 0, $offset = 0, $sortField = 'SortOrder', $sortDir = 'ASC') {
		return $this->owner->CleanTeasers("ClassName = 'CleanTeaser'")
				->limit($limit, $offset)
				->sort($sortField, $sortDir);
	}
	
	/**
	 * Tests if the count of teasers is higher than $num.
	 * 
	 * @param int $num
	 * @return bool
	 */
	public function MoreTeasersThan($num = 0) {
		if (singleton('CleanTeaser')->hasExtension('CMSPublishableDataExtension')) {
			return ($this->owner->CleanTeasers("ClassName = 'CleanTeaser'")->filter(array("Published" => true))->Count() > $num);
		} else {
			return ($this->owner->CleanTeasers("ClassName = 'CleanTeaser'")->Count() > $num);
		}
	}
}