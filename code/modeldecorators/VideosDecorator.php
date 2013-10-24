<?php
/**
 * Provides your SiteTree class with has_many videos feature.
 * It will utilize CleanVideo 's.
 *
 * Add this extension to a SiteTree instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('Page', 'VideosDecorator');
 *
 * @package cleanutilities
 * @subpackage modeldecorators
 *
 * @author arillo
 */
class VideosDecorator extends DataObjectDecorator{
	function extraStatics() {
		return array(
			'has_many' => array(
				'CleanVideos' => 'CleanVideo'
			),
		);
	}
	/*
	 * Adds the DataObjectManager to crud this SiteTree 's videos
	 */
	public function updateCMSFields(FieldSet &$fields) {
		// $ancestry = ClassInfo::dataClassesFor('CleanVideo');
		// $managedclass = $ancestry[count($ancestry)-1];
		// $domheader = array(
		// 	'VideoType' => 'Type',
		// 	'VideoAddress' => 'URL',
		// 	'VideoFileName' => 'VideoFile',
		// 	'Autoplay' => 'Autoplay'
		// );
		// if(singleton('CleanVideo')->hasExtension('CMSPublishableDecorator')){
		// 	$status = array('Status' => 'Status');
		// 	$domheader = $status+$domheader;
		// }
		$domheader = array('Autoplay' => 'Autoplay','Title'=>'Title','VideoAddress' => 'URL');
		if(CleanVideo::$use_video_upload){
			$domheader['VideoFileName']= 'VideoFile';
			$domheader['VideoType']= 'Type';
		}
		$manager = new DataObjectManager(
			$this->owner,
			'Videos',
			'CleanVideo',
			$domheader,
			'getCMSFields_forPopup',
			"ClassName = 'CleanVideo'"
		);
		$manager->setPluralTitle('Videos');
		$manager->setAddTitle('Videos');
		$fields->addFieldToTab("Root.Content.Videos", $manager);
	}
	/**
	 * Getter for the attached teasers.
	 * You can specifiy a range of those videos.
	 *
	 * @param int $offset
	 * @param int $limit
	 * @param string $sortorder
	 * @return DataObjectSet
	 */
	public function Videos($offset = 0, $limit = 0, $sortorder = 'SortOrder'){
		$range = $offset.",".$limit;
		if(!$limit){
			$range = 0;
		}
		return  $this->owner->CleanVideos("ClassName = 'CleanVideo'", "$sortorder", "", $range);
	}
	/**
	* Tests if the count of videos is higher than $num.
	*
	* @param int $num
	* @return bool
	*/
	public function MoreVideosThan($num = 0){
		if(singleton('CleanVideo')->hasExtension('CMSPublishableDecorator')){
			if($this->owner->CleanVideos("Published = 1 AND ClassName = 'CleanVideo'")->Count() > $num) return true;
			else return false;
		} else {
			if($this->owner->CleanVideos()->Count() > $num) return true;
		}
		return false;
	}
}