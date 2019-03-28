<?php
namespace Arillo\CleanUtilities\Extensions;

use SilverStripe\ORM\{
    DataExtension,
    ArrayList
};

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\{
    GridField,
    GridFieldConfig_RelationEditor,
    GridFieldAddExistingAutocompleter
};

use Colymba\BulkUpload\BulkUploader;

use  Arillo\CleanUtilities\Models\CleanImage;
use Arillo\CleanUtilities\Extensions\SortableDataExtension;

/**
 * Provides your SiteTree class with has_many images feature.
 *
 * @package cleanutilities
 * @subpackage models_extensions
 *
 * @author arillo
 */
class CleanImagesExtension extends DataExtension
{
    private static $has_many = [
        'CleanImages' => CleanImage::class
    ];

    private static $owns = [
        'CleanImages'
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $config = GridFieldConfig_RelationEditor::create();
        $config
            ->removeComponentsByType(GridFieldAddExistingAutocompleter::class)
            ->addComponent(
                (new BulkUploader())
                    ->setUfSetup('setFolderName', 'Uploads')
                    ->setAutoPublishDataObject(true)
            )
        ;
        $fields->addFieldToTab(
            'Root.Images',
            SortableDataExtension::make_gridfield_sortable(
                GridField::create(
                    'CleanImages',
                    'Images',
                    $this->owner->CleanImages(),
                    $config
                )
            )
        );
    }

    /**
     * Getter for the attached images.
     * You can specifiy a range and sorting of those images.
     *
     * @param int $limit
     * @param int $offset
     * @param string $sortField
     * @param string $sortDir
     * @return DataList
     */
    public function Images(
        $limit = 0,
        $offset = 0,
        $sortField = SortableDataExtension::SORT_FIELD,
        $sortDir = 'ASC'
    ) {
        return $this
            ->owner
            ->CleanImages()
            ->limit($limit, $offset)
            ->sort($sortField, $sortDir)
        ;
    }

    /**
     * Getter for a specific image's attachment by $index.
     *
     * @param int $index
     * @param string $sortField
     * @param string $sortDir
     * @return Image|boolean
     */
    public function ImageAttachment(
        $index = 0,
        $sortField = SortableDataExtension::SORT_FIELD,
        $sortDir = 'ASC'
    ) {
        $images = $this->owner->CleanImages()->sort($sortField, $sortDir);
        $images = $images->toArray();
        if (count($images) > $index) {
            return $images[$index]->Attachment();
        }
        return false;
    }

    /**
     * Getter for a sortable range of images's attachments.
     *
     * @param int $limit
     * @param int $offset
     * @param string $sortField
     * @param string $sortDir
     * @return DataList|boolean
     */
    public function ImagesAttachment(
        $limit = 0,
        $offset = 0,
        $sortField = SortableDataExtension::SORT_FIELD,
        $sortDir = 'ASC'
    ) {
        $images = $this->owner->CleanImages()
            ->limit($limit, $offset)
            ->sort($sortField, $sortDir);
        $images = $images->toArray();
        $result = ArrayList::create();
        foreach ($images as $file) {
            $result->push($file->Attachment());
        }
        return ($result->Count() > 0) ? $result : false;
    }

    /**
     * Tests if the count of images is higher than $num.
     *
     * @param int $num
     * @return boolean
     */
    public function MoreImagesThan($num = 0)
    {
        return ($this->owner->CleanImages()->Count() > $num);
    }
}
