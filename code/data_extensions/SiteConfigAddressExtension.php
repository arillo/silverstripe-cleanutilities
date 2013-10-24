<?php
/**
 * Generates general contact data for the client.
 *
 * Add this extension to a SiteConfig instance
 * by adding this to your _config.php:
 * 
 * Object::add_extension('SiteConfig', 'SiteConfigAddressExtension');
 * 
 * @package cleanutilities
 * @subpackage data_extensions
 * 
 * @author arillo
 */
class SiteConfigAddressExtension extends DataExtension {
	
	static $db = array(
		'Company' => 'Text',
		'Address' => 'Text',
		'Country' => 'Text',
		'ZIP' => 'Text',
		'City' => 'Text',
		'Telephone' => 'Text',
		'Cell' => 'Text',
		'Fax' => 'Text',
		'Email' => 'Text'
	);
	
	public function updateCMSFields(FieldList $fields) {
		$fields->addFieldToTab("Root.Main", TextField::create("Company", _t('SiteConfig.COMPANY', "Company")));
		$fields->addFieldToTab("Root.Main", TextField::create("Address", _t('SiteConfig.ADDRESS', "Address")));
		$fields->addFieldToTab("Root.Main", TextField::create("Country", _t('SiteConfig.COUNTRY', "Country")));
		$fields->addFieldToTab("Root.Main", TextField::create("ZIP", _t('SiteConfig.ZIP', "ZIP")));
		$fields->addFieldToTab("Root.Main", TextField::create("City", _t('SiteConfig.CITY', "City")));
		$fields->addFieldToTab("Root.Main", TextField::create("Telephone", _t('SiteConfig.TELEPHONE', "Telephone")));
		$fields->addFieldToTab("Root.Main", TextField::create("Cell", _t('SiteConfig.Cell', "Cell")));
		$fields->addFieldToTab("Root.Main", TextField::create("Fax", _t('SiteConfig.FAX', "Fax")));
		$fields->addFieldToTab("Root.Main", TextField::create("Email", _t('SiteConfig.EMAIL', "Email")));
	}
	
	/**
	 * Email encoded in html character entities.
	 * 
	 * @return string
	 */
	public function EncodedEmail() {
		return CleanUtils::html_obfuscate($this->owner->Email);
	}
}
