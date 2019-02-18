<?php
namespace Arillo\CleanUtilities\Models;

use SilverStripe\ORM\Dataobject;
use SilverStripe\Assets\File;
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
class CleanFile extends DataObject
{
    private static $table_name = 'CleanFile';

    private static $db = [
        'Title'=> 'Text'
    ];

    private static $has_one = [
        'Attachment' => File::class,
        'Reference' => SiteTree::class,
    ];

    private static $searchable_fields = [
        'Title',
        "Attachment.Extension",
        'Attachment.Title'
    ];

    private static $summary_fields = [
        'Title' => 'Title',
        'Attachment.Extension' => 'Type',
        'Attachment.Title' => 'Attachment'
    ];

    /**
     * Allowed file extensions for uploading.
     * @var array
     */
    private static $allowed_extensions = [
        '','html','htm','xhtml','js','css',
        'bmp','png','gif','jpg','jpeg','ico','pcx','tif','tiff',
        'au','mid','midi','mpa','mp3','ogg','m4a','ra','wma','wav','cda',
        'avi','mpg','mpeg','asf','wmv','m4v','mov','mkv','mp4','swf','flv','ram','rm',
        'doc','docx','txt','rtf','xls','xlsx','pages',
        'ppt','pptx','pps','csv',
        'cab','arj','tar','zip','zipx','sit','sitx','gz','tgz','bz2','ace','arc','pkg','dmg','hqx','jar',
        'xml','pdf',
    ];

    /**
     * This var specifies the name of the upload folder
     * @var string
     */
    private static $upload_folder = "files";

    /**
     * CMS fields, can be extended by write your
     * own updateCMSFields function.
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = FieldList::create(TabSet::create('Root'));
        $fields->addFieldToTab(
            'Root.Main',
            TextField::create(
                'Title',
                _t('CleanUtilities.TITLE', 'Title')
            )
        );

        $upload = UploadField::create(
            'Attachment',
            _t('CleanFile.FILE', 'File')
        );

        $upload->getValidator()->setAllowedExtensions($this->config()->allowed_extensions);
        $upload->setFolderName($this->config()->upload_folder);

        $fields->addFieldToTab('Root.Main', $upload);

        $this->extend('updateCMSFields', $fields);
        return $fields;
    }

    /**
     * Returns a download link like:
     * URLSegment/download/ClassName/ID
     *
     * To make this to work you need to implement a "download" function in
     * the Reference's controller.
     * This can be achieved by using DownloadExtension.
     *
     * @return string
     */
    public function DownloadLink()
    {
        return Controller::join_links(
            $this->Reference()->Link('download'),
            $this->ClassName,
            $this->ID
        );
    }

    /**
     * Returns a absolute download link like:
     * http://domain.com/URLSegment/download/ClassName/ID
     *
     * To make this to work you need to implement a "download" function in
     * the Reference's controller.
     * This can be achieved by using DownloadExtension.
     *
     * @return string
     */
    public function AbsoluteDownloadLink()
    {
        return Director::absoluteURL(
            $this->DownloadLink()
        );
    }
}
