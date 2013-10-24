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
class CleanTeaser extends DataObject {

	static $db = array(
		'Title'=> 'Text',
		'Description' => 'HTMLText'
	);
	
	static $has_one = array(
		'RelatedPage' => 'SiteTree',
		'Reference' => 'SiteTree',
		'Image' => 'Image'
	);
	
	static $has_many = array(
		'Links' => 'CleanTeaserLink'
	);
	
	static $searchable_fields = array(
		'Title',
		'Reference.Title'
	);

	static $summary_fields = array(
		'Title',
		'Description' => 'Description',
		'Thumbnail' => 'Thumbnail'
	);

	public static $upload_folder = "Teaser";

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->removeByName('ReferenceID');
		$fields->removeFieldFromTab('Root.Links', 'Links');
		
		$fields->removeByName('Image');
		$upload = UploadField::create(
			'Attachment',
			_t('CleanUtilities.IMAGE', 'Image')
		);
		$upload->setConfig('allowedMaxFileNumber', 1);
		$upload->getValidator()->setAllowedExtensions(
			Image::$allowed_extensions
		);
		if($this->hasExtension('ControlledFolderDataExtension')) {
			$upload->setFolderName($this->getUploadFolder());
		} else {
			$upload->setFolderName(self::$upload_folder);
		}
		$fields->insertBefore($upload, 'Title');
		$relpage = $fields->dataFieldByName('RelatedPageID');
		$fields->removeByName('RelatedPageID');
		$fields->insertBefore($relpage, 'Title');

		if ($this->ID) {
			$sortable = singleton('CleanTeaserLink')->hasExtension('SortableDataExtension');
			$config = GridFieldConfig_RelationEditor::create();
			$config->addComponent($gridFieldForm = new GridFieldDetailForm());
			$gridFieldForm->setTemplate('CMSGridFieldPopupForms');
			if ($sortable) {
				$config->addComponent(new GridFieldSortableRows('SortOrder'));
			}
			$links = $this->owner->Links();
			if ($sortable) {
				$links->sort('SortOrder');
			}
			$gridField = GridField::create('Links', 'CleanTeaser', $links, $config);
			$fields->addFieldToTab('Root.Links', $gridField);
		}
		$this->extend('updateCMSFields', $fields);
		return $fields;
	}
	
	public function getCleanDescription() {
		return strip_tags($this->Description);
	}
	
	/**
	 * Returns CMS thumbnail, if an image is attached.
	 * Mainly used by GridField.
	 *
	 * @return mixed
	 */
	public function getThumbnail() {
		if ($image = $this->Image()) {
			return $image->CMSThumbnail();
		}
		return _t('CleanTeaser.NO_IMAGE', '(No Image)');
	}
}