<?php
/**
 * Provides your SiteTree class with has_many images feature.
 * It will utilize CleanImage 's.
 *
 * Add this extension to a SiteTree instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('Page', 'ImagesDecorator');
 *
 * @package cleanutilities
 * @subpackage modeldecorators
 *
 * @author arillo
 */
class ImagesDecorator extends DataObjectDecorator{

	/**
	 * Adds has-many relation to this SiteTree class
	 */
	function extraStatics() {
		return array(
			'has_many' => array(
				'CleanImages' => 'CleanImage'
			),
		);
	}

	/**
	 * Adds the DataObjectManager to crud this SiteTree 's images
	 */
	public function updateCMSFields(FieldSet &$fields) {
		$ancestry = ClassInfo::dataClassesFor('CleanImage');
		$managedclass = $ancestry[count($ancestry)-1];
		$manager = new ImageDataObjectManager(
			$this->owner,
			'CleanImages',
			$managedclass,
			'Attachment',
			array(
				'Thumbnail' => 'Thumbnail',
				'Title' => 'Title'
			),
			'getCMSFields_forPopup'
		);
		$manager->setPageSize(999);
		$manager->setPerPageMap(array());
		$manager->setPluralTitle('Images');
		$manager->setAddTitle('Images');
		$manager->setUploadFolder($this->owner->ControlledUploadFolder('/images/'));
		$fields->addFieldToTab("Root.Content.Images", $manager);
	}

	/**
	 * Getter for the attached images.
	 * You can specifiy a range of those images.
	 *
	 * @param int $offset
	 * @param int $limit
	 * @return DataObjectSet
	 */
	public function Images($offset = 0, $limit = 0){
		$range = $offset.",".$limit;
		if(!$limit){
			$range = 0;
		}
		return  $this->owner->CleanImages("", "", "", $range);
	}
	/**
	 * Getter for a specific image's attachment by $index.
	 *
	 * @param int $index
	 * @return Image
	 */
	public function ImageAttachment($index = 0){
		$images = $this->owner->CleanImages()->toArray();
		if(count($images) > $index){
			return $images[$index]->Attachment();
		}

		return false;
	}

	/**
	 * Getter for a range of images's attachments.
	 *
	 * @param int $offset
	 * @param int $limit
	 * @return DataObjectSet
	 */
	public function ImagesAttachment($offset = 0, $limit = 0){
		$range = $offset.",".$limit;
		if(!$limit){
			$range = 0;
		}
		$images =  $this->owner->CleanImages("", "", "", $range);
		$arr = array();
		foreach($images as $image){
			$arr[] = $image->Attachment();
		}
		return new DataObjectSet($arr);
	}

	/**
	 * Tests if the count of images is higher than $num.
	 *
	 * @param int $num
	 * @return bool
	 */
	public function MoreImagesThan($num = 0){
		if($this->owner->CleanImages()->Count() > $num){
			return true;
		}
		return false;
	}
}