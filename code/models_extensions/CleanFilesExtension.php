<?php 
/**
 * Provides your SiteTree class with has_many files feature.
 * It will use CleanFiles.
 * 
 * Add this extension to a SiteTree instance
 * by adding this to your _config.php:
 * 
 * Object::add_extension('Page', 'CleanFilesExtension');
 * 
 * 
 * @package cleanutilities
 * @subpackage models_extensions
 * 
 * @author arillo
 */
class CleanFilesExtension extends DataExtension {
	
	public static $has_many = array(
		'CleanFiles' => 'CleanFile'
	);
	
	public function updateCMSFields(FieldList $fields) {
		$sortable = singleton('CleanFile')->hasExtension('SortableDataExtension');
		$config = GridFieldConfig_RelationEditor::create();
		$config->addComponent($gridFieldForm = new GridFieldDetailForm()); 
		if ($sortable) {
			$config->addComponent(new GridFieldSortableRows('SortOrder'));
		}
		if (ClassInfo::exists('GridFieldBulkFileUpload')) {
			$iu = new GridFieldBulkFileUpload('AttachmentID');
			if(singleton('CleanFile')->hasExtension('ControlledFolderDataExtension')) {
				$iu->setConfig(
					'folderName',
					singleton('CleanFile')->getUploadFolder()
				);
			} else {
				$iu->setConfig(
					'folderName',
					CleanFile::$upload_folder
				);
			}
			$config->addComponent($iu);
		}
		if ($sortable) {
			$data = $this->owner->CleanFiles()->sort('SortOrder');
		} else {
			$data = $this->owner->CleanFiles();
		}
		$fields->addFieldToTab(
			"Root.Files",
			GridField::create('CleanFiles', 'CleanFile', $data, $config)
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
	public function Files($limit = 0, $offset = 0, $sortField = 'SortOrder', $sortDir = 'ASC') {
		return $this->owner->CleanFiles()
				->limit($limit, $offset)
				->sort($sortField, $sortDir);
	}

	/**
	* Getter for a specific file's attachment by $index.
	 * 
	 * @param int $index
	 * @param string $sortField
	 * @param string $sortDir
	 * @return File|boolean
	 */
	public function FileAttachment($index = 0, $sortField = 'SortOrder', $sortDir = 'ASC') {
		$files = $this->owner->CleanFiles()->sort($sortField, $sortDir);
		$files = $files->toArray();
		if (count($files) > $index) {
			return $files[$index]->Attachment();
		}
		return false;
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
	public function FilesAttachment($limit = 0, $offset = 0, $sortField = 'SortOrder', $sortDir = 'ASC'){
		$files = $this->owner->CleanFiles()
			->limit($limit, $offset)
			->sort($sortField, $sortDir);
		$result = ArrayList::create();
		foreach ($files as $file) {
			$result->push($file->Attachment());
		}
		return ($result->Count() > 0) ? $result : false;
	}

	/**
	 * Tests if the count of files is higher than $num.
	 * 
	 * @param int $num
	 * @return boolean
	 */
	public function MoreFilesThan($num = 0) {
		return ($this->owner->CleanFiles()->Count() > $num);
	}
}