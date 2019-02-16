<?php
namespace Arillo\CleanUtilities\Extensions;

use  Arillo\CleanUtilities\Models\CleanImage;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\{
    DataExtension,
    ArrayList
};

/**
 * Provides your SiteTree class with has_many images feature.
 * It uses CleanImages.
 *
 * Add this extension to a SiteTree instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('Page', 'CleanImagesExtension');
 *
 *
 * @package cleanutilities
 * @subpackage models_extensions
 *
 * @author arillo
 */
class CleanImagesExtension extends DataExtension
{
    private static $has_many = array(
        'CleanImages' => CleanImage::class
    );

    public function updateCMSFields(FieldList $fields)
    {
        $inst = CleanImage::singleton();
        $sortable = $inst->hasExtension('SortableDataExtension');
        $config = GridFieldConfig_RelationEditor::create();
        $config->addComponent($gridFieldForm = new GridFieldDetailForm());

        if ($sortable) {
            $config->addComponent(new GridFieldSortableRows('SortOrder'));
        }

        if (ClassInfo::exists('GridFieldBulkUpload')) {
            $iu = new GridFieldBulkUpload('Attachment');
            if ($inst->hasExtension('ControlledFolderDataExtension')) {
                $iu->setUfConfig(
                    'folderName',
                    $inst->getUploadFolder()
                );
            } else {
                $iu->setUfConfig(
                    'folderName',
                    $inst->config()->upload_folder
                );
            }
            $config->addComponent($iu);
        }

        if ($sortable) {
            $data = $this->owner->CleanImages()->sort('SortOrder');
        } else {
            $data = $this->owner->CleanImages();
        }

        $fields->addFieldToTab(
            "Root.Images",
            GridField::create('CleanImages', 'CleanImage', $data, $config)
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
    public function Images($limit = 0, $offset = 0, $sortField = 'SortOrder', $sortDir = 'ASC')
    {
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
    public function ImageAttachment($index = 0, $sortField = 'SortOrder', $sortDir = 'ASC')
    {
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
    public function ImagesAttachment($limit = 0, $offset = 0, $sortField = 'SortOrder', $sortDir = 'ASC')
    {
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
