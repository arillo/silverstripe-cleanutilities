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
		'RelatedPage' => 'SiteTree',
		'Reference' => 'SiteTree',
		'Image' => 'Image'
	);

	static $has_many = array (
		'Links' => 'CleanTeaserLink'
	);

	static $searchable_fields = array(
		'Title',
		'Reference.Title'
	);

	static $summary_fields = array(
		'Title'
	);

	public function getCMSFields_forPopup(){
		$upload = new ImageUploadField('Image');
		$upload->setUploadFolder($this->ControlledUploadFolder('/teasers/'));
		$links = new DataObjectManager(
			$this,
			'Links',
			'CleanTeaserLink',
			array(
				'Title' => 'Title',
				'URL' => 'URL',
				'Type' => 'Type'
			),
			'getCMSFields_forPopup'
		);
		if(isset($_SESSION['CMSMain']['currentPage'])){
			$currPage = DataObject::get_by_id("SiteTree", $_SESSION['CMSMain']['currentPage']);
			if($currPage) Translatable::set_current_locale($currPage->Locale);
		}
		$relpag = new SimpleTreeDropDownField("RelatedPageID","Related Page","SiteTree","","Title","","Please Select");
		$relpag->setFilter("ClassName != 'ErrorPage'");
		$fields = new FieldSet(
			new TextField('Title','Title'),
			new SimpleTinyMCEField('Description','Description'),
			$relpag,
			$upload,
			$links
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