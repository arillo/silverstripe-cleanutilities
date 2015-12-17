<?php
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
        'CleanVideos' => 'CleanVideo'
    );
    
    public function updateCMSFields(FieldList $fields)
    {
        $sortable = singleton('CleanVideo')->hasExtension('SortableDataExtension');
        $config = GridFieldConfig_RelationEditor::create();
        $config->addComponent($gridFieldForm = new GridFieldDetailForm());

        if ($sortable) {
            $config->addComponent(new GridFieldSortableRows('SortOrder'));
        }

        if (ClassInfo::exists('GridFieldBulkFileUpload')) {
            $iu = new GridFieldBulkFileUpload('VideoFileID');
            if (singleton('CleanVideo')->hasExtension('ControlledFolderDataExtension')) {
                $iu->setUfConfig(
                    'folderName',
                    singleton('CleanVideo')->getUploadFolder()
                );
            } else {
                $iu->setUfConfig(
                    'folderName',
                    CleanVideo::$upload_folder
                );
            }
            $config->addComponent($iu);
        }

        if ($sortable) {
            $data = $this->owner->CleanVideos("ClassName = 'CleanVideo'")->sort('SortOrder');
        } else {
            $data = $this->owner->CleanVideos("ClassName = 'CleanVideo'");
        }

        $fields->addFieldToTab(
            "Root.Videos",
            GridField::create('CleanVideos', 'CleanVideo', $data, $config)
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
    public function Videos($limit = 0, $offset = 0, $sortField = 'SortOrder', $sortDir = 'ASC')
    {
        return $this->owner->CleanVideos("ClassName = 'CleanVideo'")
                ->limit($limit, $offset)
                ->sort($sortField, $sortDir);
    }

    /**
     * Tests if the count of videos is higher than $num.
     * 
     * @param int $num
     * @return bool
     */
    public function MoreVideosThan($num = 0)
    {
        return ($this->owner->CleanVideos("ClassName = 'CleanVideo'")->Count() > $num);
    }
}
