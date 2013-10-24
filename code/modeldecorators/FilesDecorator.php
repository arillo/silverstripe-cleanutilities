<?php
/**
 * Provides your SiteTree class with has_many files feature.
 * It will utilize CleanFile 's.
 *
 * Add this extension to a SiteTree instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('Page', 'FilesDecorator');
 *
 *
 * @package cleanutilities
 * @subpackage modeldecorators
 *
 * @author arillo
 */
class FilesDecorator extends DataObjectDecorator{

	/**
	 * Adds has-many relation to this SiteTree class
	 */
	function extraStatics() {
		return array(
			'has_many' => array(
				'CleanFiles' => 'CleanFile'
			),
		);
	}

	/**
	 * Adds the DataObjectManager to crud this SiteTree 's files
	 */
	public function updateCMSFields(FieldSet &$fields) {
		// $manager = new FileDataObjectManager(
		// 	$this->owner,
		// 	'CleanFiles',
		// 	'CleanFile',
		// 	'Attachment',
		// 	array(
		// 		'Title' => 'Title'
		// 	),
		// 	'getCMSFields_forPopup',
		// 	"ClassName = 'CleanFile'"
		// );
		$manager = new DataObjectManager(
			$this->owner,
			'CleanFiles',
			'CleanFile',
			array(
				'Title' => 'Title',
				'Attachment.Filename' => 'File'
			),
			'getCMSFields_forPopup',
			"ClassName = 'CleanFile'"
		);
		$manager->setPluralTitle('Files');
		$manager->setAddTitle('Files');
		// $manager->setUploadFolder($this->owner->ControlledUploadFolder('/files/'));
		$fields->addFieldToTab("Root.Content.Files", $manager);
	}

	/**
	 * Getter for the attached files.
	 * You can specifiy a range of those files.
	 *
	 * @param int $offset
	 * @param int $limit
	 * @return DataObjectSet
	 */
	public function Files($offset = 0, $limit = 0){
		$range = $offset.",".$limit;
		if(!$limit){
			$range = 0;
		}
		return  $this->owner->CleanFiles("ClassName = 'CleanFile'", "", "", $range);
	}

	/**
	* Getter for a specific file's attachment by $index.
	*
	* @param int $index
	* @return File
	*/
	public function FileAttachment($index = 0){
		$images = $this->owner->CleanFiles("ClassName = 'CleanFile'")->toArray();
		if(count($images) > $index){
			return $images[$index]->Attachment();
		}

		return false;
	}

	/**
	 * Getter for a range of file's attachments.
	 *
	 * @param int $offset
	 * @param int $limit
	 * @return DataObjectSet
	 */
	public function FilesAttachment($offset = 0, $limit = 1){
		$range = $offset.",".$limit;
		if(!$limit){
			$range = 0;
		}
		$images =  $this->owner->CleanFiles("ClassName = 'CleanFile'", "", "", $range);
		$arr = array();
		foreach($images as $image){
			$arr[] = $image->Attachment();
		}
		return new DataObjectSet($arr);
	}
	/**
	* Tests if the count of files is higher than $num.
	*
	* @param int $num
	* @return bool
	*/
	public function MoreFilesThan($num = 0){
		if($this->owner->CleanFiles("ClassName = 'CleanFile'")->Count() > $num){
			return true;
		}
		return false;
	}
}