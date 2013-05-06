<?php
/**
 * Provides with some modulo helper functionality for
 * DataObjects when they are within a DataObjectSet.
 *
 * Add this extension to a DataObject instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('DataObject', 'ModuloDecorator');
 *
 * @package cleanutilities
 * @subpackage decorators
 *
 * @author arillo
 */
class ModuloDecorator extends DataObjectDecorator {

	/**
	 * Indicator for, if this object is the nth child of a collection.
	 *
	 * @param int $modulo
	 * @return bool
	 */
	public function GetModulo($modulo){
		$rest = ($this->owner->Pos())%$modulo;
		if($rest == 0) return true;

		return false;
	}

	/**
	 * Indicator for, if this object is before the nth child of a collection.
	 *
	 * @param int $modulo
	 * @return bool
	 */
	public function GetBeforeModulo($modulo){
		$rest = ($this->owner->Pos(0))%$modulo;
		if($rest == 0) return true;

		return false;
	}

	public function LessThan($num){
		if($this->Pos(0) < $num) return true;

		return false;
	}
}