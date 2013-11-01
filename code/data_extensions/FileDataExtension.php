<?php 
/**
 * Provides extra functionality to File.
 *
 * Add this extension to a File instance
 * by adding this to your _config.php:
 * 
 * Object::add_extension('File', 'FileDataExtension');
 * 
 * @package cleanutilities
 * @subpackage utilitiesdecorators
 * 
 * @author arillo
 */
class FileDataExtension extends DataExtension {

	/**
	 * Reads and returns the creation date of the file. 
	 * 
	 * @return Datetime
	 */
	public function FileCreationDate() {
		if (file_exists($this->owner->getFullPath())) {
			return DBField::create_field('Datetime', date("F d Y H:i:s.", filectime($this->owner->getFullPath())));
		}
		return _t('FileDataExtension.NO_DATE', 'No date');
		
	}
}