<?php
namespace Arillo\CleanUtilities\Extensions;

use Arillo\CleanUtilities\Models\CleanVideo;

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
 * Provides your SiteTree class with has_many videos feature.
 * It uses CleanVideos.
 *
 * Add this extension to a SiteTree instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('Page', 'CleanVideosExtension');
 *
 * @package cleanutilities
 * @subpackage models_extensions
 *
 * @author arillo
 */
class CleanVideosExtension extends DataExtension
{

    private static $has_many = array(
        'CleanVideos' => CleanVideo::class
    );

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldToTab(
            'Root.Videos',
            SortableDataExtension::make_gridfield_sortable(
                GridField::create(
                    'CleanVideos',
                    'Videos',
                    $this->owner->CleanVideos(),
                    GridFieldConfig_RelationEditor::create()
                )
            )
        );
    }

    /**
     * Getter for the attached teasers.
     * You can specifiy a range of those videos.
     *
     * @param int $limit
     * @param int $offset
     * @param string $sortField
     * @param string $sortDir
     * @return DataList
     */
    public function Videos(
        $limit = 0,
        $offset = 0,
        $sortField = 'SortOrder',
        $sortDir = 'ASC'
    ) {
        return $this
            ->owner
            ->CleanVideos()
            ->limit($limit, $offset)
            ->sort($sortField, $sortDir)
        ;
    }

    /**
     * Tests if the count of videos is higher than $num.
     *
     * @param int $num
     * @return bool
     */
    public function MoreVideosThan($num = 0)
    {
        return ($this->owner->CleanVideos()->Count() > $num);
    }
}
