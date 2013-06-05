<?php 
/**
 * Provides SortOrder to a DataObject.
 * 
 * Add this extension to a SiteTree instance
 * by adding this to your _config.php:
 * 
 * Object::add_extension('CleanFile', 'SortableDataExtension');
 * 
 * 
 * @package cleanutilities
 * @subpackage models_extensions
 * 
 * @author arillo
 */
class SortableDataExtension extends DataExtension {
	
	static $db = array(
		'SortOrder' => 'Int'
	);
	
	public static $default_sort = 'SortOrder';

	/**
	 * Remove SortOrder from CMSFields
	 * @param  FieldList $fields
	 */
	function updateCMSFields(FieldList $fields) {
		$fields->removeByName('SortOrder');
	}
}