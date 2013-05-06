<?php
/**
 * Provides extra functionality to Folder.
 *
 * Add this extension to a Folder instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('Folder', 'FolderDecorator');
 *
 * @package cleanutilities
 * @subpackage decorators
 *
 * @author arillo
 */
class FolderDecorator extends DataObjectDecorator{

	/**
	 * Get the children sorted by name of this folder that are also folders.
	 *
	 * @return DataObjectSet
	 */
	function SortedChildFolders(){ return DataObject::get("Folder", "\"ParentID\" = " . (int)$this->owner->ID, "Title ASC"); }

	/**
	 * All subfolders sorted by $sort.
	 *
	 *
	 * @param $sort
	 * @return DataObjectSet
	 */
	public function sortedChildren($sort = "Title ASC") {
		// Ugly, but functional.
		$ancestors = ClassInfo::ancestry($this->owner->class);
		foreach($ancestors as $i => $a) {
			if(isset($baseClass) && $baseClass === -1) {
				$baseClass = $a;
				break;
			}
			if($a == "DataObject") $baseClass = -1;
		}

		$g = DataObject::get($baseClass, "\"ParentID\" = " . $this->owner->ID, $sort);
		return $g;
	}
}