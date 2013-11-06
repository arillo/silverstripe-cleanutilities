<?php
/**
 * Provides PublishDate for Pages
 * 
 * Add this extension to a SiteTree instance
 * by adding this to your _config.php:
 * 
 * Object::add_extension('Page', 'PublishDateDataExtension');
 * 
 * @package cleanutilities
 * @subpackage data_extensions
 * 
 * @author arillo
 */
class PublishDateDataExtension extends DataExtension {
	
	private static $db = array(
		'PublishDate' => 'Datetime'
	);
	
	/**
	 * Automatically sets PublishDate to now, if is empty.
	 */
	public function onBeforeWrite() {
		if ($this->owner->ID) {
			if ($this->owner->PublishDate == '') {
				$this->owner->PublishDate = date('Y-m-d H:i:s', strtotime('now'));
			}
		}
		parent::onBeforeWrite();
	}
	/**
	 * Adds PublishDate to CMS Form.
	 * @param $fields
	 */
	function updateCMSFields(FieldList $fields) {
		$datefield = DateField::create(
			'PublishDate', 
			_t('CMSPublishableDataExtension.PUBLISH_DATE', 'Publish date')
		);
		$datefield->setConfig('setLocale', 'en_US');
		$datefield->setConfig('showcalendar', 1);
		$fields->addFieldToTab("Root.Main", $datefield, 'Content');
	}
}