<?php
namespace Arillo\CleanUtilities\Models;

use SilverStripe\ORM\Dataobject;
use SilverStripe\Assets\Image;
use SilverStripe\CMS\Model\SiteTree;

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

    public function getCMSFields()
    {
        $fields = FieldList::create(TabSet::create('Root'));
        $fields->addFieldToTab(
            'Root.Main',
            TextField::create('Title',
                _t('CleanUtilities.TITLE', 'Title')
            )
        );

        Fields::add_image_field($this, $fields);

        $this->extend('updateCMSFields', $fields);
        return $fields;
    }
}
