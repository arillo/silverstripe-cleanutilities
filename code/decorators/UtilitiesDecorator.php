<?php
/**
 * Provides a couple of helper methods to
 * the SiteTree instances.
 *
 * Add this extension to a SiteTree instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('Page', 'UtilitiesDecorator');
 *
 * @package cleanutilities
 * @subpackage decorators
 *
 * @author arillo
 */
class UtilitiesDecorator extends DataObjectDecorator{

	/**
	 * Sets i18n locale and adds Content-language to meta tags.
	 * @param string $locale
	 */
	public static function setup_locale($locale){
	   	Requirements::insertHeadTags('<meta http-equiv="Content-language" content="' . i18n::get_lang_from_locale($locale) . '" />');
		i18n::set_locale($locale);
	}

	/**
	 * Adds new Fields to this Page.
	 * Fields:
	 *   'PublishDate' => 'Datetime'
	 *
	 * @return array
	 */
	function extraStatics() {
		return array(
			'db' => array(
				'PublishDate' => 'Datetime'
			)
		);
	}

	/**
	 * Automatically sets PublishDate to now, if is empty.
	 */
	public function onBeforeWrite() {
		if($this->owner->ID){
			if($this->owner->PublishDate == ''){
				$this->owner->PublishDate = date('Y-m-d H:i:s', strtotime('now'));
			}
		}
		parent::onBeforeWrite();
	}

	/**
	 * Adds PublishDate to CMS Form.
	 * @param $fields
	 */
	public function updateCMSFields(FieldSet &$fields) {
		$fields->addFieldToTab('Root.Content.Main', new LiteralField('ClassNameDescription','<h4 style="color:#000;font-size: 16px; margin-bottom: 10px; padding-top: 10px;"><span style="font-weight: normal; font-size: 14px">Locale</span>: '.$this->owner->Locale.' <span style="font-weight: normal; font-size: 14px">ID</span>: '.$this->owner->ID.' <span style="font-weight: normal; font-size: 14px">Type:</span> '.$this->owner->i18n_singular_name().'</h4>'), 'Title');
		$datefield = new DatetimeField('PublishDate', 'PublishDate');
		$datefield->getTimeField()->setConfig('showdropdown', true);
		$datefield->getDateField()->setConfig('showcalendar', true);
		$datefield->setConfig('setLocale','en_US');
		$datefield->setConfig('showcalendar',1);
		$fields->addFieldToTab("Root.Content.Main",$datefield,'Content');
	}

	/**
	 * Sets i18n locale and adds Content-language to meta tags.
	 * @param string $locale
	 */
	public function setupLocale($locale){
		Debug::show("Deprecated functionality, use UtilitiesDecorator::setup_locale() instead.");
		self::setup_locale($locale);
	}

	/**
	 * Returns all members of a group by ID .
	 *
	 * @param int $ID  group ID
	 * @return DataObjectSet
	 */
	public function MemberGroup($ID = 1){
		return DataObject::get("Member","Group_Members.GroupID={$ID}","FirstName ASC","LEFT JOIN `Group_Members` ON `Member`.`ID` = `Group_Members`.`MemberID` LEFT JOIN `Group` ON `Group`.`ID` = `Group_Members`.`GroupID`");
	}

	/**
	 * Returns a SiteTree instance by ClassName.
	 *
	 * @param string $pagetype
	 * @return mixed DataObject/bool
	 */
	public function PageInstance($pagetype = 'Page'){
		if(ClassInfo::is_subclass_of($pagetype, 'SiteTree')){
			if($page = DataObject::get_one($pagetype)) return $page;
		}
		return false;
	}

	/**
	 * Return a Page_Controller instance by page ClassName.
	 *
	 * @return mixed Page_Controller/bool
	 */
	public function PageControllerInstance($pagetype = 'Page'){
		$page = DataObject::get_one($pagetype);
		if($page){
			$controllerClass = $pagetype."_Controller";
			return new $controllerClass($page);
		}
		return false;
	}

	/**
	 * Returns shortlang from Locale.
	 *
	 * @return string
	 */
	public function ShortLang(){
		return i18n::get_lang_from_locale($this->owner->Locale);
	}

}