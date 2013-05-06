<?php

/**
 * A simple decorator class that adds translatable fields to a given DataObject subclass.
 * Unlike the {@link Translatable} module, this class does not require a CMS interface
 * and therefore can be applied beyond SiteTree decendants.
 * Good use case are small DataObjects in has_many/ many_many relations,
 * when it comes to translations.
 *
 * It supports static $db fields only.
 *
 * SS2.4 Port by Bumbus
 *
 * @todo: Does not support $has_one relations.
 *
 *
 * <b>Usage</b>:
 *
 * ---------- MyDataObject.php ----------
 * <code>
 * static $db = array (
 *	'Title' => 'Varchar',
 *  'Description' => 'Text'
 * );
 * </code>
 *
 *
 * ------------- _config.php ------------
 * <code>
 * TranslatableDataObjectDecorator::set_locales(array(
 *	'en_GB',
 *	'fr_FR',
 *	'it_IT'
 * ));
 *
 * TranslatableDataObjectDecorator::register('MyDataObject', array(
 * 	'Title',
 *  'Description'
 * ));
 * </code>
 *
 * Always run /dev/build after adding new locales.
 *
 *
 * ---------- MyDataObject::getCMSFields() -------------
 * <code>
 *  // Option 1: Add all translations for all fields to fieldset
 *	foreach(TranslatableDataObjectDecorator::get_translation_fields($this) as $field) {
 *		$f->push($field);
 *	}
 *
 * </code>
 *
 *
 * -------------------- Template ---------------------
 * Use the $T() function to get the translation for a given field.
 * <code>
 * <h2>$T(Title)</h2>
 * <p>$T(Description)</p>
 * </code>
 *
 *
 *
 */
class TranslatableDataObjectDecorator extends DataObjectDecorator{

	/**
	 * @var array A list of all the locales that are registered as translations
	 */
	public static $locales = array();


	/**
	 * @var array Stores all the classes that are registered as translatable and their
	 * 			  associated $db arrays.
	 */
	public static $translation_manifest = array();


	/**
	 * Given a field name and a locale name, create a composite string to represent
	 * the field in the database.
	 *
	 * @param string $field The field name
	 * @param string $locale The locale name
	 * @return string
	 */
	public static function i18n_field($field, $locale = null){
		if(!$locale) $locale = i18n::get_locale();
		return "{$field}__{$locale}";
	}


	/**
	 * Adds translatable locales
	 *
	 * @param mixed A list of locales, either as an array or argument list
	 */
	public static function set_locales(){
		$args = func_get_args();
		if(empty($args)){
			trigger_error("TranslatableDataObjectDecorator::set_locales() called with no arguments.",E_USER_ERROR);
		}
		$locales = (isset($args[0]) && is_array($args[0])) ? $args[0] : $args;
		foreach($locales as $l){
			if(!i18n::validate_locale($l)) trigger_error("TranslatableDataObjectDecorator::set_locales() -- Locale '$l' is not a valid locale.", E_USER_ERROR);
			self::$locales[$l] = $l;
		}
	}



	/**
	 * Given a translatable field name, pull out the locale and
	 * return the raw field name.
	 *
	 * ex: "Description__fr_FR" -> "Description"
	 *
	 * @param string $field The name of the translated field
	 * @return string
	 */
	public static function get_basename($field){ return reset(explode("__", $field)); }


	/**
	 * Given a translatable field name, pull out the raw field name and
	 * return the locale
	 *
	 * ex: "Description__fr_FR" -> "fr_FR"
	 *
	 * @param string $field The name of the translated field
	 * @return string
	 */
	public static function get_locale($field){ return end(explode("__", $field)); }



	/**
	 * Registers a class as translatable and adds translatable columns
	 * to a given list of fields
	 *
	 * @param string $class The class to register as translatable
	 * @param array $fields The list of fields to translate (must all exist in $db)
	 */
	public static function register($class, $fields = array()){
		self::$translation_manifest[$class] = array();
		$SNG = singleton($class);
		foreach($fields as $f){
			$db = $SNG->stat('db');
			if($type = $db[$f]) foreach(self::$locales as $locale) self::$translation_manifest[$class][self::i18n_field($f, $locale)] = $type;
		}
		Object::add_extension($class, 'TranslatableDataObjectDecorator');
	}

	/**
	 * Get translation formfields for an instance
	 */
	public static  function get_translation_fields($instance, $forDOM = true, $filter_name = null, $filter_locale = null){
		$fields = array ();
		foreach(self::$translation_manifest[$instance->ClassName] as $field => $type){
			if($filter_name && $filter_name != self::get_basename($field)) continue;
			foreach(self::$locales as $locale){
				if($filter_locale && $filter_locale != $locale) continue;
				if($o = $instance->obj($field)){
					//Debug::dump($o->Name);
					$formField = $o->scaffoldFormField(self::get_basename($o->Name) . " (" . self::get_locale($o->Name) . ")");
					if($forDOM && ClassInfo::exists('SimpleHTMLEditorField')){
						if($formField instanceof HTMLEditorField){
							$formField = new SimpleHTMLEditorField($formField->Name());
						}
					}
					$fields[] = $formField;
				}
			}
		}
		return $fields;
	}

	/**
	 * Dynamically generate the $db array for a class given all of its
	 * registered translations
	 *
	 * @param string $class The class that is being decorated
	 */
	function extraStatics($class = null){
		if(isset(self::$translation_manifest[$class])){
			return array (
				'db' => self::$translation_manifest[$class]		
			);
		}
	}
	
	/**
	 * TODO: 
	 *	set default field for default locale to defautl field value (e.g. $Title => $Title__de_DE) 
	 
	public function onBeforeWrite(){
		//i18n_field
		
		parent::onBeforeWrite();
	}
	*/
	public static function set_default_sort($class, $field, $sort, $locale){
		$inst = new $class();
		$sortField = $inst->i18n_field($field, $locale) ? $inst->i18n_field($field, $locale) : "Title";
		$inst::$default_sort = "{$sortField} {$sort}";
	}
	
	function updateFieldLabels(&$lables){
		$extra_fields = $this->extraStatics($this->owner->ClassName);
		if(isset($extra_fields['field_labels'])){
			$field_labels = $extra_fields['field_labels'];
			if($field_labels) $lables = array_merge($lables, $field_labels);
		}
	}

	/**
	 * A template accessor used to get the translated version of a given field
	 *
	 * ex: $T(Description) in the locale it_IT returns $yourClass->obj('Description__it_IT');
	 *
	 * @param string $field The field name to translate
	 * @return string
	 */
	public function T($field){
		$locale = Controller::curr()->CurrentPage()->Locale;
		$i18nField = self::i18n_field($field, $locale);
		$result = $this->owner->getField($field);
		if($this->owner->hasField($i18nField)){
			$result =  $this->owner->getField($i18nField) != "" ?  $this->owner->getField($i18nField) : $result;
		}
		return $result;
	}
}