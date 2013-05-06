<?php
/**
 * Provides SiteTree classes with a language menu.
 *
 * Add this extension to a SiteTree instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('Page', 'LanguageDecorator');
 *
 * @package cleanutilities
 * @subpackage decorators
 *
 * @author arillo
 */
class LanguageDecorator extends DataObjectDecorator{

	/**
	 * Returns a DataObjectSet containing Pages.
	 * The provided links point to their translated pages.
	 * You can use it in templates like this:
	 *
	 * <% control LanguageChooser %>
	 *   $Title, $Current, and any other vars in your page instance
	 * <% end_control %>
	 *
	 * @return DataObjectSet
	 */
	public function LanguageChooser(){

		if(!Controller::curr()) return;

		$langs = Translatable::get_allowed_locales();
		$data = new DataObjectSet();
		foreach($langs as $key => $code) {
			if($code == Translatable::get_current_locale()){
				$this->owner->Current = 'current';
				$data->push($this->owner);
			} else {
				$translation = $this->owner->getTranslation($code);
				if($translation){
					$data->push($translation);
				} else {
					$page = Translatable::get_one_by_locale("SiteTree", $code,"URLSegment LIKE 'home%'");
					if($page) $data->push($page);
				}
			}
		}
		return $data;
	}
}