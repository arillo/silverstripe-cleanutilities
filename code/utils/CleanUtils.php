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
	 * Return current users IP
	 * 
	 * @return string
	 */
	public static function get_ip() {
		$ip = "";
		if ($_SERVER) {
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
		} else {
			if(getenv('HTTP_X_FORWARDED_FOR')){
				$ip = getenv('HTTP_X_FORWARDED_FOR');
			} else if (getenv('HTTP_CLIENT_IP')) {
				$ip = getenv('HTTP_CLIENT_IP');
			} else {
				$ip = getenv('REMOTE_ADDR');
			}
		}
		return $ip;
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
	public static function encode_email($email = ''){
		$output = "";
		for($i = 0; $i < strlen($email); $i++) $output .= '&#'.ord($email[$i]).';';
		return $output;
	}
	public static function update_manager_header($dom = null,$header = null){
		if($dom!=null && $header!=null){
			$cn = $dom->class;
			return new $cn(
				$this,
				$dom->Name(),
				$dom->sourceClass(),
				$header,
				'getCMSFields_forPopup'
			);
		}
		return false;
	}
	public static function create_manager_for($relationname = null,$reference = null,$header = null){
		if($relationname!=null && $reference!=null && $header!=null){
			$hasmanyclass = $reference->has_many($relationname);
			$manymanyclass = $reference->many_many($relationname);
			if(isset($hasmanyclass)){
				$manager = new DataObjectManager(
					$reference,
					$relationname,
					$hasmanyclass,
					$header,
					'getCMsFields_forPopup'
				);
			}
			if(isset($manymanyclass)){
				$manager = new ManyManyDataObjectManager(
					$reference,
					$relationname,
					$hasmanyclass,
					$header,
					'getCMsFields_forPopup'
				);
			}
			return $manager;
		}
		return false;
	}
}