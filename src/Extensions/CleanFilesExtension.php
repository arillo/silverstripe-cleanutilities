<?php
namespace Arillo\CleanUtilities\Extensions;

use SilverStripe\ORM\{
    DataExtension,
    ArrayList
};

use SilverStripe\Forms\GridField\{
    GridField,
    GridFieldConfig_RelationEditor
};

use SilverStripe\Forms\FieldList;
use Arillo\CleanUtilities\Models\CleanFile;
use Arillo\CleanUtilities\Extensions\SortableDataExtension;

/**
 * Provides your SiteTree class with has_many files feature.
 * It will use CleanFiles.
 *
 * @package cleanutilities
 * @subpackage models_extensions
 *
 * @author arillo
 */
class CleanFilesExtension extends DataExtension
{
    private static
        $has_many = [
            'CleanFiles' => CleanFile::class
        ]
    ;

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldToTab(
            'Root.Files',
            SortableDataExtension::make_gridfield_sortable(
                GridField::create(
                    'CleanFiles',
                    'Files',
                    $this->owner->CleanFiles(),
                    GridFieldConfig_RelationEditor::create()
                )
            )
        );
    }
    /**
     * Getter for the attached files.
     * You can specifiy a range and sorting of those files.
     *
     * @param int $limit
     * @param int $offset
     * @param string $sortField
     * @param string $sortDir
     * @return DataList
     */
    public function Files(
        $limit = 0,
        $offset = 0,
        $sortField = 'SortOrder',
        $sortDir = 'ASC'
    ) {
        return $this
            ->owner
            ->CleanFiles()
            ->limit($limit, $offset)
            ->sort($sortField, $sortDir)
        ;
    }

    /**
    * Getter for a specific file's attachment by $index.
     *
     * @param int $index
     * @param string $sortField
     * @param string $sortDir
     * @return File|null
     */
    public function FileAttachment(
        $index = 0,
        $sortField = 'SortOrder',
        $sortDir = 'ASC'
    ) {
        $files = $this
            ->owner
            ->CleanFiles()
            ->sort($sortField, $sortDir)
        ;

        $files = $files->toArray();

        if (count($files) > $index) return $files[$index]->Attachment();

        return null;
    }

    /**
     * Getter for a sortable range of file's attachments.
     *
     * @param int $limit
     * @param int $offset
     * @param string $sortField
     * @param string $sortDir
     * @return ArrayList|boolean
     */
    public function FilesAttachment(
        $limit = 0,
        $offset = 0,
        $sortField = 'SortOrder',
        $sortDir = 'ASC'
    ) {
        $files = $this->owner->CleanFiles()
            ->limit($limit, $offset)
            ->sort($sortField, $sortDir);

        $result = ArrayList::create();

        foreach ($files as $file) $result->push($file->Attachment());

        return ($result->Count() > 0) ? $result : false;
    }

    /**
     * Tests if the count of files is higher than $num.
     *
     * @param int $num
     * @return boolean
     */
    public function MoreFilesThan($num = 0)
    {
        return ($this->owner->CleanFiles()->Count() > $num);
    }
}
