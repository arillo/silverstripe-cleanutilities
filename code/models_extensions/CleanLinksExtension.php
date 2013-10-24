<?php
/**
 * Provides your SiteTree class with has_many links feature.
 * It uses CleanLinks.
 * 
 * Add this extension to a SiteTree instance
 * by adding this to your _config.php:
 * 
 * Object::add_extension('Page', 'CleanLinksExtension');
 * 
 * @package cleanutilities
 * @subpackage models_extensions
 * 
 * @author arillo
 */
class CleanLinksExtension extends DataExtension {
	
	static $has_many = array(
		'CleanLinks' => 'CleanLink'
	);
	
	public function updateCMSFields(FieldList $fields) {
		$sortable = singleton('CleanLink')->hasExtension('SortableDataExtension');
		$config = GridFieldConfig_RelationEditor::create();
		$config->addComponent($gridFieldForm = new GridFieldDetailForm());
		
		if ($sortable) {
			$config->addComponent(new GridFieldSortableRows('SortOrder'));
		}
		if ($sortable) {
			$data = $this->owner->CleanLinks()->sort('SortOrder');
		} else {
			$data = $this->owner->CleanLinks();
		}
		$fields->addFieldToTab(
			"Root.Links",
			GridField::create('CleanLinks', 'CleanLink', $data, $config)
		);
	}
	
	/**
	 * Getter for the attached links.
	 * You can specifiy a range of those links.
	 * 
	 * @param int $limit
	 * @param int $offset
	 * @param string $sortField
	 * @param string $sortDir
	 * @return DataList
	 */
	public function Links($limit = 0, $offset = 0, $sortField = 'SortOrder', $sortDir = 'ASC') {
		return $this->owner->CleanLinks()
			->limit($limit, $offset)
			->sort($sortField, $sortDir);
	}

	/**
	 * Tests if the count of links is higher than $num.
	 * 
	 * @param int $num
	 * @return bool
	 */
	public function MoreLinksThan($num = 0) {
		return ($this->owner->CleanLinks()->Count() > $num);
	}
}