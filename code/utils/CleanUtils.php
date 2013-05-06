<?php
/**
 * Helpers.
 *
 * @package cleanutilities
 * @subpackage utils
 *
 * @author arillo
 */
class CleanUtils{
	/**
	 * Define the foldername for this module.
	 * @var string
	 */
	public static $module = "cleanutilities";
	/**
	 * Helper function, which adds the given $cssClass to all
	 * formfields in a given $fieldset, specified by $requiredFields.
	 *
	 * @param FieldSet $fieldset
	 * @param array $requiredFields
	 * @param string $cssClass
	public static function add_required_css($fieldset, $requiredFields, $cssClass = "required" ){
		foreach($requiredFields as $f){
			try{
				$fieldset->dataFieldByName($f)->addExtraClass($cssClass);
			}catch(Exception $err){
				Debug::show("Error: Field " . $f . " does not exist in given fieldlist");
			}
		}
	}
	*/

	/**
	 * Like PHPs instance_of but the SS way of doing it.
	 *
	 * @param string $class
	 * @param string $parentClass
	 * @return bool
	 */
	public static function instance_of($class, $parentClass){ return (ClassInfo::is_subclass_of($class, $parentClass) || $class == $parentClass); }

	/**
	 * Generates an url friendly representation of a given string.
	 *
	 * @param string $title
	 * @return string
	 */
	public static function generate_urlsegment($title){
		$t = (function_exists('mb_strtolower')) ? mb_strtolower($title) : strtolower($title);
		$t = Object::create('Transliterator')->toASCII($t);
		$t = str_replace('&amp;','-and-',$t);
		$t = str_replace('&','-and-',$t);
		$t = ereg_replace('[^A-Za-z0-9]+','-',$t);
		$t = ereg_replace('-+','-',$t);
		$t = trim($t, '-');
		return $t;
	}
	/**
	 * Generates an url friendly representation of a given string.
	 * Will replace all "illegal" characters with a _ .
	 *
	 * @param string $string
	 * @return string
	 */
	public static function string_to_underscored_name($string){
		$string = preg_replace('/[\'"]/', '', $string);
		$string = preg_replace('/[^a-zA-Z0-9]+/', '_', $string);
		$string = trim($string, '_');
		$string = strtolower($string);
		return $string;
	}

	/**
	 * Removes all alphanumeric and punctual characters from
	 * the given $string.
	 *
	 * @param string $string
	 * @return string
	 */
	public static function clean_name($string){
		return preg_replace("/[^[:alnum:][:punct:]]/", "", $string);
	}
}