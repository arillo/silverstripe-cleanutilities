<?php
namespace Arillo\CleanUtilities\Extensions;

use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\FieldList;

/**
 * Provides SortOrder to a DataObject.
 *
 * Add this extension to a SiteTree instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('CleanFile', 'SortableDataExtension');
 *
 *
 * @package cleanutilities
 * @subpackage models_extensions
 *
 * @author arillo
 */
class SortableDataExtension extends DataExtension
{

    private static $db = array(
        'SortOrder' => 'Int'
    );

    private static $default_sort = 'SortOrder';

    /**
     * Remove SortOrder from CMSFields
     * @param  FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        $fields->removeByName('SortOrder');
    }
}
