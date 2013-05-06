<?php
/**
 * Provides extra functionality to DataObjectSets.
 *
 * Add this extension to a Controller instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('DataObjectSet', 'DataObjectSetDecorator');
 *
 * @package cleanutilities
 * @subpackage decorators
 *
 * @author arillo
 */
class DataObjectSetDecorator extends DataObjectDecorator{

	/**
	 * Sorting on this DataObjectSet by a
	 * compound string "[FIELD] [Direction]"
	 * like: "Title ASC"
	 *
	 * @param string $sort
	 * @return DataObjectSet
	 */
	public function SortedBy($sort){
		$data = explode(" ", $sort);
		if(isset($data[1])) $this->owner->sort($data[0],$data[1]);
		else $this->owner->sort($data[0]);

		return $this->owner;
	}

	/**
	 * Returns a range from this DataObjectSet.
	 * $param should be formated as compund [START_INDEX]_[LENGTH]
	 *
	 * @param string $param
	 * @return DataObjectSet
	 */
	public function Range($param = ""){
		if($param != ""){
			$data = explode("_",$param);
			return $this->owner->getRange($data[0], $data[1]);
		}
		return $this->owner;
	}
}