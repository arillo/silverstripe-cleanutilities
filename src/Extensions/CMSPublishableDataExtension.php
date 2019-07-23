<?php
namespace Arillo\CleanUtilities\Extensions;

use SilverStripe\Control\Controller;
use SilverStripe\CMS\Controllers\CMSPageEditController;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\Queries\SQLSelect;
use SilverStripe\ORM\DataQuery;

use SilverStripe\Forms\{
    FieldList,
    CheckboxField
};


/**
 * Provides publish/unpublish functionality to DataObjects.
 *
 * Add this extension to a DataObject instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('CleanTeaser', 'CMSPublishableDataExtension');
 *
 * @package cleanutilities
 * @subpackage data_extensions
 *
 * @author arillo
 */
class CMSPublishableDataExtension extends DataExtension
{

    private static $db = array(
        'Published' => 'Boolean'
    );

    public function populateDefaults()
    {
        $this->owner->Published = true;
    }

    public function updateCMSFields(FieldList $fields)
    {
        $fields->removeByName('Published');
        $fields->unshift(
            CheckboxField::create(
                'Published',
                _t('CMSPublishableDataExtension.PUBLISHED', 'Published')
            )
        );
        return $fields;
    }

    /**
     * Filter out all unpublished items
     * @param  SQLQuery $query
     */
    public function augmentSQL(SQLSelect $query, DataQuery $dataQuery = null)
    {
        if (Controller::curr() != CMSPageEditController::class) {
            $query->addWhere("Published=1");
        }
    }

    /**
     * Returns an inactive Checkbox, as indicator
     * useful feature for GridField etc.
     *
     * @return string
     */
    public function getPublishStatus()
    {
        return CheckboxField::create('PublishStatus')
            ->setValue($this->owner->Published)
            ->setDisabled(true);
    }
}
