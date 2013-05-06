<?php
/**
 * Provides SiteTree with extra an extra menu.
 *
 * Add this extension to a SiteConfig instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('Page', 'SecondMenuDecorator');
 *
 * @package cleanutilities
 * @subpackage decorators
 *
 * @author arillo
 */
class SecondMenuDecorator extends DataObjectDecorator{

	/**
	 * Adds fields to your site config.
	 *
	 * Fields:
	 *   'SecondMenu' => 'Boolean'
	 *
	 * @return array
	 */
	function extraStatics() {
		return array(
			'db' => array(
				'SecondMenu' => 'Boolean'
			)
		);
	}

	/**
	 * Adds the new fields to CMS form.
	 *
	 * @param $fields
	 */
	public function updateCMSFields(FieldSet &$fields){
		$fields->addFieldToTab('Root.Behaviour', new CheckboxField('SecondMenu','Show in second menu?'),'ProvideComments');
	}

	/**
	 * Returns all SiteTree instances which have SecondMenu activated.
	 *
	 * @param int $parent
	 * @return DataObjectSet
	 */
	public function SecondMenu($parent = 0){
		$where = "SecondMenu = 1";
		if($parent) $where .= " AND ParentID = 0";
		$result = DataObject::get("SiteTree", $where);
		$visible = array();
		if(isset($result)){
			foreach($result as $page){
				if($page->can('view')) $visible[] = $page;
			}
		}
		return new DataObjectSet($visible);
	}
}