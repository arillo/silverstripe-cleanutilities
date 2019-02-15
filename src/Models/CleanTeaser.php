<?php
/**
 * A DataObject for Teasers
 * Provides Title, Description and an Image
 *
 * @package cleanutilities
 * @subpackage models
 *
 * @author arillo
 */
class CleanTeaser extends DataObject
{

    private static $db = array(
        'Title'=> 'Text',
        'Description' => 'HTMLText'
    );
    
    private static $has_one = array(
        'Reference' => 'SiteTree',
        'Image' => 'Image'
    );
    
    private static $searchable_fields = array(
        'Title',
        'Reference.Title'
    );

    private static $summary_fields = array(
        'Title',
        'Description' => 'Description',
        'Thumbnail' => 'Thumbnail'
    );

    public static $upload_folder = "Teaser";

    public function getCMSFields()
    {
        $fields = FieldList::create(
            new TabSet(
                "Root",
                new Tab("Main")
            )
        );
        $fields->addFieldToTab('Root.Main', TextField::create('Title', 'Title'));
        $fields->addFieldToTab('Root.Main', TextareaField::create('Description', 'Description'));
        $upload = UploadField::create('Image', 'Image');
        $upload->setConfig('allowedMaxFileNumber', 1);
        $upload->getValidator()->setAllowedExtensions(
            CleanImage::$allowed_extensions
        );
        if ($this->hasExtension('ControlledFolderDataExtension')) {
            $upload->setFolderName($this->getUploadFolder());
        } else {
            $upload->setFolderName(self::$upload_folder);
        }
        $fields->addFieldToTab(
            'Root.Main',
            $upload
        );
        $this->extend('updateCMSFields', $fields);
        return $fields;
    }
    public function getCleanDescription()
    {
        return strip_tags($this->Description);
    }
    
    /**
     * Returns CMS thumbnail, if an image is attached.
     * Mainly used by GridField.
     *
     * @return mixed
     */
    public function getThumbnail()
    {
        if ($image = $this->Image()) {
            return $image->CMSThumbnail();
        }
        return _t('CleanTeaser.NO_IMAGE', '(No Image)');
    }
}
