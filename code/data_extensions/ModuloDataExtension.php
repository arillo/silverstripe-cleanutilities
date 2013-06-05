<?php
/**
 * Provides with some modulo helper functionality for
 * use in templates.
 *
 * Add this extension to a DataObject instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('DataObject', 'ModuloDataExtension');
 *
 * @package cleanutilities
 * @subpackage data_extensions
 *
 * @author arillo
 */
class ModuloDataExtension extends DataExtension {
	
	/**
	 * Indicator for, if this object is the nth child of a collection.
	 *
	 * @param int $pos current position in the list
	 * @param int $modulo
	 * @return bool
	 */
	public function GetModulo($pos, $modulo) {
		return (($pos%$modulo) == 0);
	}
	
	/**
	 * Indicator for, if this object is before the nth child of a collection.
	 *
	 * @param int $pos current position in the list
	 * @param int $modulo
	 * @return bool
	 */
	public function GetBeforeModulo($pos, $modulo) {
		return (($pos-1) % $modulo == 0);
	}
	
	/**
	 * Tests if current position is smaller than a 
	 * given value.
	 * 
	 * @param int $pos current position in the list
	 * @param int $num
	 * @return bool
	 */
	public function LessThan($pos, $num) {
		return ($pos < $num);
	}
}