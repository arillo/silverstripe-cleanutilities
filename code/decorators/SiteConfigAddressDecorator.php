<?php
/**
 * Generates general contact data for the client.
 *
 * Add this extension to a SiteConfig instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('SiteConfig', 'SiteConfigAddressDecorator');
 *
 * @package cleanutilities
 * @subpackage decorators
 *
 * @author arillo
 */
class SiteConfigAddressDecorator extends DataObjectDecorator{

	/**
	 * Adds fields to your site config.
	 *
	 * Fields:
	 *   'Company' => 'Text',
	 *   'Address' => 'Text',
	 *	 'Country' => 'Text',
	 *	 'PLZ' => 'Text',
	 *	 'City' => 'Text',
	 *	 'Telephone' => 'Text',
	 *	 'Cell' => 'Text',
	 *	 'Fax' => 'Text',
	 *	 'Email' => 'Text'
	 *
	 * @return array
	 */
	function extraStatics() {
		return array(
			'db' => array(
				'Company' => 'Text',
				'Address' => 'Text',
				'Country' => 'Text',
				'PLZ' => 'Text',
				'City' => 'Text',
				'Telephone' => 'Text',
				'Cell' => 'Text',
				'Fax' => 'Text',
				'Email' => 'Text'
			),
		);
	}

	/**
	 * Adds the new fields to CMS form.
	 *
	 * @param $fields
	 */
	public function updateCMSFields(FieldSet &$fields) {
		$fields->addFieldToTab("Root.Main", new TextField("Company", _t('SiteConfig.COMPANY', "Company")));
		$fields->addFieldToTab("Root.Main", new TextField("Address", _t('SiteConfig.ADDRESS', "Address")));
		$fields->addFieldToTab("Root.Main", new TextField("Country", _t('SiteConfig.COUNTRY', "Country")));
		$fields->addFieldToTab("Root.Main", new TextField("PLZ", _t('SiteConfig.PLZ', "PLZ")));
		$fields->addFieldToTab("Root.Main", new TextField("City", _t('SiteConfig.CITY', "City")));
		$fields->addFieldToTab("Root.Main", new TextField("Telephone", _t('SiteConfig.TELEPHONE', "Telephone")));
		$fields->addFieldToTab("Root.Main", new TextField("Cell", _t('SiteConfig.Cell', "Cell")));
		$fields->addFieldToTab("Root.Main", new TextField("Fax", _t('SiteConfig.FAX', "Fax")));
		$fields->addFieldToTab("Root.Main", new TextField("Email", _t('SiteConfig.EMAIL', "Email")));
	}

	/**
	 * Returns an encoded version of Email field.
	 * @return string
	 */
	public function EncodedEmail(){
		$output = "";
		for($i = 0; $i < strlen($this->owner->Email); $i++) $output .= '&#'.ord($this->owner->Email[$i]).';';
		return $output;
	}
}
