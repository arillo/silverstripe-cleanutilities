<?php
/**
 * A DataObject for Teasers
 * Provides Title, Description, an Image and many Links
 *
 * @package cleanutilities
 * @subpackage models
 *
 * @author arillo
 */
class CleanTeaser extends DataObject{

	static $db = array(
		'Title'=> 'Text',
		'Description' => 'HTMLText'
	);

	static $has_one = array(
		'Reference' => 'Page',
		'Image' => 'Image'
	);

	static $searchable_fields = array(
		'Title',
		'Reference.Title'
	);

	static $summary_fields = array(
		'Title'
	);

	public function getCMSFields_forPopup(){
		$upload = new ImageUploadField("Image","Image");
		$upload->setUploadFolder($this->ControlledUploadFolder('/teasers/'));
		$fields = new FieldSet(
			new Tabset('Root',
				new Tab('Main',
					new TextField('Title','Title'),
					new SimpleTinyMCEField('Description','Description'),
					$upload
				)
			)
		);
		$this->extend('updateCMSFields_forPopup', $fields);
		return $fields;
	}
	/**
	 * Returns CMS thumbnail, if an image is attached.
	 * Mainly used by DataObjectManager.
	 *
	 * @return mixed
	 */
	function getThumbnail(){
		if ($image = $this->Image()) return $image->CMSThumbnail();
		return _t('CleanTeaser.NO_IMAGE', '(No Image)');
	}
}