<?php
namespace Arillo\CleanUtilities\Extensions;

use Arillo\CleanUtilities\Models\CleanLink;

use SilverStripe\ORM\{
    DataExtension,
    ArrayList
};

use SilverStripe\Forms\GridField\{
    GridField,
    GridFieldConfig_RelationEditor
};

use SilverStripe\Forms\FieldList;
use Arillo\CleanUtilities\Extensions\SortableDataExtension;

/**
 * Provides your SiteTree class with has_many links feature.
 * It uses CleanLinks.
 *
 * Add this extension to a SiteTree instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('Page', 'CleanLinksExtension');
 *
 * @package cleanutilities
 * @subpackage models_extensions
 *
 * @author arillo
 */
class CleanLinksExtension extends DataExtension
{

    private static $has_many = array(
        'CleanLinks' => CleanLink::class
    );

    public function updateCMSFields(FieldList $fields)
    {
        $sortable = CleanLink::singleton()->hasExtension('SortableDataExtension');
        $config = GridFieldConfig_RelationEditor::create();

        if ($sortable) {
            $config->addComponent(new GridFieldSortableRows('SortOrder'));
        }

        $data = $this->owner->CleanLinks();
        if ($sortable) $data = $data->sort('SortOrder');

        $fields->addFieldToTab(
            "Root.Links",
            $gridField = GridField::create('CleanLinks', 'Links', $data, $config)
        );

        if ($sortable) SortableDataExtension::make_gridfield_sortable($gridField);
    }

    /**
     * Getter for the attached links.
     * You can specifiy a range of those links.
     *
     * @param int $limit
     * @param int $offset
     * @param string $sortField
     * @param string $sortDir
     * @return DataList
     */
    public function Links(
        $limit = 0,
        $offset = 0,
        $sortField = 'SortOrder',
        $sortDir = 'ASC'
    ) {
        return $this
            ->owner
            ->CleanLinks()
            ->limit($limit, $offset)
            ->sort($sortField, $sortDir)
        ;
    }

    /**
     * Tests if the count of links is higher than $num.
     *
     * @param int $num
     * @return bool
     */
    public function MoreLinksThan($num = 0)
    {
        return ($this->owner->CleanLinks()->Count() > $num);
    }
}
