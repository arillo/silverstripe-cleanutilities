<?php
/**
 * Provides extra fields to Group,
 * to make custom redirects after login possible.
 *
 * Add this extension to a Group instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('Group', 'GroupLoginDecorator');
 *
 * @package cleanutilities
 * @subpackage decorators
 *
 * @author arillo
 */
class GroupLoginDecorator extends DataObjectDecorator {

	function augmentSQL(SQLQuery &$query) {}

	public function extraStatics(){
		return array(
			'db' => array(
				"GoToAdmin" => "Boolean"
			),
			'has_one' => array(
				"LinkPage" => "SiteTree"
			),
		);
	}

	public function updateCMSFields(FieldSet &$fields) {
		$fields->addFieldToTab("Root.Members", new CheckboxField("GoToAdmin", " Go to Admin area"), 'Members');
		$fields->addFieldToTab("Root.Members", new TreeDropdownField("LinkPageID", "Or select a Page to redirect to", "SiteTree"), 'Members');
	}
}