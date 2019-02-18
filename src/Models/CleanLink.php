<?php
namespace Arillo\CleanUtilities\Models;

use SilverStripe\ORM\Dataobject;
use SilverStripe\CMS\Model\SiteTree;

use SilverStripe\Forms\{
    FieldList,
    TextField,
    TabSet,
    DropdownField
};

/**
 * A DataObject for Links
 *
 * @package cleanutilities
 * @subpackage models
 *
 * @author arillo
 */
class CleanLink extends DataObject
{
    private static $table_name = 'CleanLink';

    private static $db = array(
        'Title' => 'Text',
        'URL' => 'Varchar(255)',
        'Target' => "Enum('_blank,_self','_blank')"
    );

    private static $has_one = array(
        'Reference' => SiteTree::class
    );

    private static $searchable_fields = array(
        'Title',
        'URL',
        'Reference.Title'
    );

    private static $summary_fields = array(
        'Title' => 'Title',
        'URL' => 'URL',
        'Target' => 'Target'
    );

    public function getCMSFields()
    {
        $fields = FieldList::create(TabSet::create('Root'));

        $fields->addFieldsToTab('Root.Main', [
            TextField::create(
                'Title',
                _t('CleanUtilities.Title', 'Title')
            ),
            TextField::create(
                'URL',
                _t('CleanUtilities.URL', 'Url')
            ),
            DropdownField::create(
                'Target',
                _t('CleanLink.TARGET', 'Choose the target'),
                $this->dbObject('Target')->enumValues()
            )
        ]);

        $this->extend('updateCMSFields', $fields);
        return $fields;
    }
}
