<?php
namespace Arillo\CleanUtilities\Extensions;

use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\FieldList;

use SilverStripe\Forms\GridField\GridField;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

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
    const SORT_FIELD = 'SortOrder';

    private static $db = array(
        self::SORT_FIELD => 'Int'
    );

    private static $default_sort = self::SORT_FIELD;

    /**
     * Add GridFieldOrderableRows to a GridField.
     *
     * @param  GridField $gridField
     * @param  string    $sortField
     * @return GridField
     */
    public static function make_gridfield_sortable(
        GridField $gridField,
        string $sortField = self::SORT_FIELD
    ) {
        $gridField
            ->getConfig()
            ->addComponent(new GridFieldOrderableRows($sortField))
        ;

        return $gridField;
    }


    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        if (!$this->owner->{self::SORT_FIELD})
        {
            $this->owner->{self::SORT_FIELD} = get_class($this->owner)::get()->max(self::SORT_FIELD) + 1;
        }
    }

    /**
     * Remove SortOrder from CMSFields
     * @param  FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        $fields->removeByName('SortOrder');
    }
}
