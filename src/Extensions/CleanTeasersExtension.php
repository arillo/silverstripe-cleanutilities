<?php
namespace Arillo\CleanUtilities\Extensions;

use Arillo\CleanUtilities\Models\CleanTeaser;

use SilverStripe\ORM\{
    DataExtension,
    ArrayList
};

use SilverStripe\Forms\GridField\{
    GridField,
    GridFieldConfig_RelationEditor
};

use SilverStripe\Forms\FieldList;

/**
 * Provides your SiteTree class with has_many teasers feature.
 * It will utilize CleanTeaser 's.
 *
 * Add this extension to a SiteTree instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('Page', 'CleanTeasersExtension');
 *
 * @package cleanutilities
 * @subpackage models_extensions
 *
 * @author arillo
 */
class CleanTeasersExtension extends DataExtension
{

    private static $has_many = array(
        'CleanTeasers' => CleanTeaser::class
    );

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldToTab(
            'Root.Teasers',
            SortableDataExtension::make_gridfield_sortable(
                GridField::create(
                    'CleanTeasers',
                    'Teasers',
                    $this->owner->CleanTeasers(),
                    GridFieldConfig_RelationEditor::create()
                )
            )
        );
    }

    /**
     * Getter for the attached teasers.
     * You can specifiy a range of those links.
     *
     * @param int $limit
     * @param int $offset
     * @param string $sortField
     * @param string $sortDir
     */
    public function Teasers(
        $limit = 0,
        $offset = 0,
        $sortField = 'SortOrder',
        $sortDir = 'ASC'
    ) {
        return $this
            ->owner
            ->CleanTeasers()
            ->limit($limit, $offset)
            ->sort($sortField, $sortDir)
        ;
    }

    /**
     * Tests if the count of teasers is higher than $num.
     *
     * @param int $num
     * @return bool
     */
    public function MoreTeasersThan($num = 0)
    {
        if (CleanTeaser::singleton()->hasExtension('CMSPublishableDataExtension')) {
            return ($this->owner->CleanTeasers()->filter(array("Published" => true))->Count() > $num);
        } else {
            return ($this->owner->CleanTeasers()->Count() > $num);
        }
    }
}
