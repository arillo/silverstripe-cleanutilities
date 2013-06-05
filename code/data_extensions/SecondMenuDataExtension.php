<?php 
/**
 * Provides SiteTree with extra an extra menu.
 *
 * Add this extension to a SiteConfig instance
 * by adding this to your _config.php:
 * 
 * Object::add_extension('SiteTree', 'SecondMenuDataExtension');
 * 
 * @package cleanutilities
 * @subpackage data_extensions
 * 
 * @author arillo
 */
class SecondMenuDataExtension extends DataExtension {
	
	static $db = array(
		'SecondMenu' => 'Boolean'
	);
	
	public function updateSettingsFields(FieldList $fields) {
		$fields->addFieldToTab(
			'Root.Settings',
			CheckboxField::create(
				'SecondMenu',
				_t('SecondMenuDataExtension.SHOW_IN_SECONDMENU', 'Show in second menu?')
			),
			'ShowInSearch'
		);
		return $fields;
	}
	
	/**
	 * Returns all SiteTree instances which have SecondMenu activated. 
	 * 
	 * @param int $parentID
	 * @return ArrayList
	 */
	public function SecondMenu($parentID = 0) {
		$filter = array("SecondMenu" => true);
		if ($parentID != 0) {
			$filter["ParentID"] = $parentID;
		}
		$result = SiteTree::get()->filter($filter);
		if ($result->Count() > 0) {
			$visible = new ArrayList();
			foreach($result as $page) {
				if($page->can('view')) $visible->push($page);
			}
			return $visible;
		}
		return false;
	}
}