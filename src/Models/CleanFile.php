<?php
namespace Arillo\CleanUtilities\Models;

use SilverStripe\ORM\Dataobject;
use SilverStripe\Assets\File;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\AssetAdmin\Forms\UploadField;

use SilverStripe\Control\Director;

use SilverStripe\Forms\{
    FieldList,
    TextField,
    TabSet
};

use Arillo\CleanUtilities\CMS\Fields;

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

    private static $owns = [
        'Attachment',
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

        Fields::add_file_field($this, $fields);

        $this->extend('updateCMSFields', $fields);
        return $fields;
    }

    public function DownloadLink()
    {
        return $this->Attachment()->Link();
    }

    public function AbsoluteDownloadLink()
    {
        return Director::absoluteURL(
            $this->DownloadLink()
        );
    }
}
