<?php 
/**
 * Provides extra functionality to Folder.
 *
 * Add this extension to a Folder instance
 * by adding this to your _config.php:
 * 
 * Object::add_extension('Folder', 'FolderDataExtension');
 * 
 * @package cleanutilities
 * @subpackage data_extensions
 * 
 * @author arillo
 */
class FolderDataExtension extends DataExtension
{
    
    /**
     * Get the children sorted by name of this folder that are also folders.
     * 
     * @return ArrayList
     */
    public function getSortedChildFolders($sortField = 'Title', $sortDir = 'ASC')
    {
        return Folder::get()
                ->filter(array(
                    'ParentID' => (int)$this->owner->ID
                ))
                ->sort($sortField, $sortDir);
    }
    
    /**
     * All subfolders and files sorted by $sort.
     * 
     * @param $sort
     * @return DataList
     */
    public function getSortedChildren($sortField = 'Title', $sortDir = 'ASC')
    {
        $ancestors = ClassInfo::ancestry($this->owner->class);
        foreach ($ancestors as $i => $a) {
            if (isset($baseClass) && $baseClass === -1) {
                $baseClass = $a;
                break;
            }
            if ($a == "DataObject") {
                $baseClass = -1;
            }
        }
        return $baseClass::get()
                ->filter(array(
                    "ParentID" => $this->owner->ID
                ))
                ->sort($sortField, $sortDir);
    }
}
