<?php
namespace Arillo\CleanUtilities\Models;

use SilverStripe\ORM\Dataobject;
use SilverStripe\Assets\Image;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\AssetAdmin\Forms\UploadField;

use SilverStripe\Control\{
    Controller,
    Director
};

use SilverStripe\Forms\{
    FieldList,
    TextField,
    TabSet
};

/**
 * A wrapper for File, which adds a Title field
 * and a relation to it's page.
 *
 * @package cleanutilities
 * @subpackage models
 *
 * @author arillo
 */
class CleanImage extends DataObject
{
    private static $table_name = 'CleanImage';

    private static $db = array(
        'Title'=> 'Text'
    );

    private static $has_one = array(
        'Attachment' => Image::class,
        'Reference' => SiteTree::class
    );

    private static $owns = array(
        'Attachment'
    );

    private static $searchable_fields = array(
        'Title',
        'Attachment.Title'
    );

    private static $summary_fields = array(
        'Attachment.CMSThumbnail' => 'Image',
        'Title' => 'Title'
    );

    /**
     * This var specifies the name of the upload folder
     * @var string
     */
    private static $upload_folder = "images";

    /**
     * Allowed file extensions for uploading.
     * @var array
     */
    private static $allowed_extensions = array(
        '', 'bmp','png','gif','jpg','jpeg','ico','pcx','tif','tiff'

    );

    public function getCMSFields()
    {
        $fields = FieldList::create(TabSet::create('Root'));
        $fields->addFieldToTab(
            'Root.Main',
            TextField::create('Title',
                _t('CleanUtilities.TITLE', 'Title')
            )
        );

        $upload = UploadField::create('Attachment', 'Image');
        // $upload->setConfig('allowedMaxFileNumber', 1);
        $upload->getValidator()->setAllowedExtensions(
            $this->config()->allowed_extensions
        );

        if ($this->hasExtension('ControlledFolderDataExtension')) {
            $upload->setFolderName($this->getUploadFolder());
        } else {
            $upload->setFolderName($this->config()->upload_folder);
        }

        $fields->addFieldToTab(
            'Root.Main',
            $upload
        );

        $this->extend('updateCMSFields', $fields);
        return $fields;
    }
}
