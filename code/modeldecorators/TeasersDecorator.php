<?php
/**
 * Provides your SiteTree class with has_many teasers feature.
 * It will utilize CleanTeaser 's.
 *
 * Add this extension to a SiteTree instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('Page', 'TeasersDecorator');
 *
 * @package cleanutilities
 * @subpackage modeldecorators
 *
 * @author arillo
 */
class TeasersDecorator extends DataObjectDecorator{

	/**
	 * Adds has-many relation to this SiteTree class
	 */
	function extraStatics() {
		return array(
			'has_many' => array(
				'CleanTeasers' => 'CleanTeaser'
			),
		);
	}

	/**
	 * Adds the DataObjectManager to crud this SiteTree 's teasers
	 */
	public function updateCMSFields(FieldSet &$fields) {
		$ancestry = ClassInfo::dataClassesFor('CleanTeaser');
		$managedclass = $ancestry[count($ancestry)-1];
		$domheader = array(
			'Thumbnail' => 'Thumbnail',
			'Title' => 'Title'
		);
		if(singleton('CleanTeaser')->hasExtension('CMSPublishableDecorator')){
			$status = array('Status' => 'Status');
			$domheader = $status+$domheader;
		}
		$manager = new DataObjectManager(
			$this->owner,
			'CleanTeasers',
			$managedclass,
			$domheader,
			'getCMSFields_forPopup'
		);
		$manager->setPluralTitle('Teasers');
		$manager->setAddTitle('Teasers');
		//$manager->setUploadFolder($this->owner->ControlledUploadFolder('/teasers/'));
		$fields->addFieldToTab("Root.Content.Teasers", $manager);
	}

	/**
	 * Getter for the attached teasers.
	 * You can specifiy a range of those links.
	 *
	 * @param int $offset
	 * @param int $limit
	 * @return DataObjectSet
	 */
	public function Teasers($offset = 0, $limit = 0){
		$range = $offset.",".$limit;
		if(!$limit){
			$range = 0;
		}
		if(singleton('CleanTeaser')->hasExtension('CMSPublishableDecorator')){
			return  $this->owner->CleanTeasers("Published = 1", "", "", $range);
		}else{
			return  $this->owner->CleanTeasers("", "", "", $range);
		}
   	}

   	/**
   	 * Tests if the count of teasers is higher than $num.
   	 *
   	 * @param int $num
   	 * @return bool
   	 */
   	public function MoreTeasersThan($num = 0){
   		if(singleton('CleanTeaser')->hasExtension('CMSPublishableDecorator')){
	   		if($this->owner->CleanTeasers("Published = 1")->Count() > $num) return true;
	   		else return false;
   		}else{
   			if($this->owner->CleanTeasers()->Count() > $num) return true;
   		}
	   	return false;
   	}
}