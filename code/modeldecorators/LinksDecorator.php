<?php
/**
 * Provides your SiteTree class with has_many links feature.
 * It will utilize CleanLink 's.
 *
 * Add this extension to a SiteTree instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('Page', 'LinksDecorator');
 *
 * @package cleanutilities
 * @subpackage modeldecorators
 *
 * @author arillo
 */
class LinksDecorator extends DataObjectDecorator{

	/**
	 * Adds has-many relation to this SiteTree class
	 */
	function extraStatics() {
		return array(
			'has_many' => array(
				'CleanLinks' => 'CleanLink'
			)
		);
	}

	/**
	 * Adds the DataObjectManager to crud this SiteTree 's links
	 */
	public function updateCMSFields(FieldSet &$fields) {
		$ancestry = ClassInfo::dataClassesFor('CleanLink');
		$managedclass = $ancestry[count($ancestry)-1];
		$manager = new DataObjectManager(
			$this->owner,
			'CleanLinks',
			$managedclass,
			array(
				'Title' => 'Title',
				'URL' => 'URL',
				'Target' => 'Target'
			),
			'getCMSFields_forPopup'
		);
		$manager->setPluralTitle('Links');
		$manager->setAddTitle('Links');
		$fields->addFieldToTab("Root.Content.Links", $manager);
	}

	/**
	 * Getter for the attached links.
	 * You can specifiy a range of those links.
	 *
	 * @param int $offset
	 * @param int $limit
	 * @return DataObjectSet
	 */
	public function Links($offset = 0, $limit = 0){
		$range = $offset.",".$limit;
		if(!$limit){
			$range = 0;
		}
		return  $this->owner->CleanLinks("", "", "", $range);
   	}

   	/**
   	 * Tests if the count of links is higher than $num.
   	 *
   	 * @param int $num
   	 * @return bool
   	 */
   	public function MoreLinksThan($num = 0){
   		if($this->owner->CleanLinks()->Count() > $num){
   			return true;
   		}
   		return false;
   	}
}