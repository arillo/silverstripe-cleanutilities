<?php
namespace Arillo\CleanUtilities\Models;

use SilverStripe\ORM\Dataobject;
use SilverStripe\Assets\Image;
use SilverStripe\CMS\Model\SiteTree;

use SilverStripe\Forms\{
    FieldList,
    TextField,
    TextareaField,
    TabSet
};

use Arillo\CleanUtilities\CMS\Fields;

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
    private static $table_name = 'CleanTeaser';

    private static $db = array(
        'Title'=> 'Text',
        'Description' => 'HTMLText'
    );

    private static $has_one = array(
        'Reference' => SiteTree::class,
        'Image' => Image::class,
    );

    private static $searchable_fields = array(
        'Title',
        'Reference.Title'
    );

    private static $summary_fields = array(
        'Title',
        'Description' => 'Description',
        'Image.CMSThumbnail' => 'Image'
    );

    public function getCMSFields()
    {
        $fields = FieldList::create(TabSet::create('Root'));
        $fields->addFieldToTab('Root.Main', TextField::create('Title', 'Title'));
        $fields->addFieldToTab('Root.Main', TextareaField::create('Description', 'Description'));

        Fields::add_file_field($this, $fields, [
            'field' => 'Image',
            'folderName' => 'Teaser',
        ]);

        $this->extend('updateCMSFields', $fields);
        return $fields;
    }

    public function getCleanDescription()
    {
        return strip_tags($this->Description);
    }
}
