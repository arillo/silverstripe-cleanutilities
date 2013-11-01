<?php
/**
 * Helpers.
 *
 * @package cleanutilities
 * @subpackage utils
 *
 * @author arillo
 */
class CleanUtils {

	/**
	 * Define the foldername for this module.
	 * @var string
	 */
	public static $module = "silverstripe-cleanutilities";

	/**
	 * Merges two or more arrays of arrays.
	 * Later parameters will override possible values of the previous.
	 * 
	 * @param  array  $array1
	 * @param  array  $array2
	 * @return array
	 */
	public static function array_extend(array $array1, array $array2) {
		$merged = $array1;
		foreach ($array2 as $key => $value) {
			if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
				$merged[$key] = self::array_extend($merged[$key], $value);
			} else {
				$merged[$key] = $value;
			}
		}
		if (func_num_args() > 2) {
			$rest = array_slice(func_get_args(), 2);
			foreach ($rest as $other) {
				if (is_array($other)) {
					$merged = self::array_extend($merged, $other);
				}
			}
		}
		return $merged;
	}

	/**
	 * Helper function, which adds the given $cssClass to all
	 * $form fields specified by its requiredfields
	 *
	 * @param Form $form
	 * @param string $cssClass
	 */
	public static function add_required_css(Form $form, $cssClass = "required"){
		if($requiredFields = $form->getValidator()->getRequired()){
			foreach($requiredFields as $f){
				if ($field = $form->Fields()->fieldByName($f)) {
					$field->addExtraClass($cssClass);
				}
			}
		}
	}

	/**
	 * A simple Gridfield factory
	 * @param  string $model
	 * @param  string $relationname
	 * @param  DataObject $reference
	 * @return GridField
	 */
	public static function create_gridfield_for($model, $relationname, $reference){
		if($relationname != null && ClassInfo::exists($model)) {
			$config = GridFieldConfig_RelationEditor::create();
			$config->addComponent($gridFieldForm = new GridFieldDetailForm());
			if ($items = $reference->$relationname()) {
				if (is_a($items, 'ManyManyList') && ClassInfo::exists('GridFieldManyRelationHandler')) {
					$config->addComponent(new GridFieldManyRelationHandler(), 'GridFieldPaginator');
				}
				$sortable = singleton($model)->hasExtension('SortableDataExtension');
				if ($sortable){
					$config->addComponent(new GridFieldSortableRows('SortOrder'));
				}
				$gridfield = GridField::create($relationname, $model, $items, $config);
				$datacolumns = $gridfield->getConfig()->getComponentByType('GridFieldDataColumns');
				$cfields = singleton($model)->summaryFields();
				if(singleton($model)->hasExtension('CMSPublishableDataExtension') && !isset($cfields['PublishStatus'])){
					$cfields = array('PublishStatus' => 'PublishStatus') + $cfields;
				}
				$datacolumns->setDisplayFields($cfields);
				return $gridfield;
			} else {
				throw new InvalidArgumentException("Couldn't find relation.");
			}
		} else {
			throw new InvalidArgumentException("Couldn't create GridField because wrong parameters passed to the factory.");
		}
	}

	/**
	 * Generates an uploadfield.
	 * 
	 * @param  string $relationname
	 * @param  string $title
	 * @param  DataObject $reference
	 * @param  array $allowed_extensions
	 * @param  string $upload_folder
	 * @return UploadField
	 */
	public static function create_uploadfield_for($relationname, $title, $reference, $allowed_extensions = null, $upload_folder=null){
		$upload = UploadField::create($relationname, $title);
		$upload->setConfig('allowedMaxFileNumber', 1);
		if($allowed_extensions!=null){
			$upload->getValidator()->setAllowedExtensions($allowed_extensions);
		}
		if($upload_folder != null){
			$upload->setFolderName($upload_folder);
		}else{
			if($reference->hasExtension('ControlledFolderDataExtension')) {
				$upload->setFolderName($reference->getUploadFolder());
			} else {
				$upload->setFolderName('uploads');
			}
		}
		return $upload;
	}

	/**
	 * Like instanceof but the SS way of doing it.
	 * 
	 * @param string $class
	 * @param string $parentClass
	 * @return bool
	 */
	public static function instance_of($class, $parentClass){
		return (is_subclass_of($class, $parentClass) || $class == $parentClass);
	}
	
	/**
	 * Generates an Url segment by a given string.
	 * You can pass a DataObject as 2nd parameter, which's ClassName
	 * and ID will be used if the filtering on title fails.
	 * 
	 * @param  string $title
	 * @param  DataObject $instance
	 * @return string
	 */
	public static function generate_urlsegment($title, DataObject $instance = null) {
		$filter = URLSegmentFilter::create();
		$t = $filter->filter($title);
		if(!$t 
			|| $t == '-' 
			|| $t == '-1'
			&& $instance != null
		){
			$t = "$instance->ClassName-$instance->ID";
		}
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
	 * Obfuscates a given string into html character entities.
	 * 
	 * @param string $text
	 * @return string
	 */
	// public static function html_obfuscate($text) {
	// 	$rv = '';
	// 	for($i = 0; $i < strlen($text); $i++){
	// 		$rv .= '&#' . ord($text[$i]) . ';';
	// 	}
	// 	return $rv;
	// } 
	
	/**
	 * Sets i18n locale and adds Content-language to meta tags.
	 * @param string $locale
	 */
	public static function setup_locale($locale = "") {
		if($locale != ""){
			Requirements::insertHeadTags('<meta http-equiv="Content-language" content="' . i18n::get_lang_from_locale($locale) . '" />');
			i18n::set_locale($locale);
		}else{
			Debug::show("Your locale is not properly set. Remember that for using this function you need to use Object::add_extension('SiteTree', 'Translatable'); in your project _config.php");
		}
	}
}