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
	 * $form fields specified by its requiredfields
	 *
	 * @param Form $form
	 * @param string $cssClass
	 */
	public static function add_required_css($form, $cssClass = "required"){
		if($requiredFields = $form->getValidator()->getRequired()){
			foreach($requiredFields as $f){
				if($field = $form->dataFieldByName($f)) {
					$field->addExtraClass($cssClass);
				}
			}
		}
	}

	/**
	 * Obfuscates a given string into html character entities.
	 *
	 * @param string $text
	 * @return string
	 */
	public static function html_obfuscate($text){
		$rv = '';
		for($i = 0; $i < strlen($text); $i++){
			$rv .= '&#' . ord($text[$i]) . ';';
		}
		return $rv;
	}

	/**
	 * Sets i18n locale and adds Content-language to meta tags.
	 * @param string $locale
	 */
	public static function setup_locale($locale = ""){
		if($locale != ""){
			Requirements::insertHeadTags('<meta http-equiv="Content-language" content="' . i18n::get_lang_from_locale($locale) . '" />');
			i18n::set_locale($locale);
		}else{
			Debug::show("Your locale is not properly set. Remember that for using this function you need to use Object::add_extension('SiteTree', 'Translatable'); in your project _config.php");
		}
	}

	/**
	 * Like PHPs instance_of but the SS way of doing it.
	 *
	 * @param string $class
	 * @param string $parentClass
	 * @return bool
	 */
	public static function instance_of($class, $parentClass){
		return (ClassInfo::is_subclass_of($class, $parentClass) || $class == $parentClass);
	}

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