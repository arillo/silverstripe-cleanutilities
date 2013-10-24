<?php 
/**
 * Provides SiteTree classes with a language menu.
 *
 * Add this extension to a SiteTree instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('Page', 'LanguageDataExtension');
 *
 * @package cleanutilities
 * @subpackage data_extensions
 *
 * @author arillo
 */
class LanguageDataExtension extends DataExtension {
	/**
	 * Returns a DataList containing Pages.
	 * The provided links point to their translated pages.
	 * You can use it in templates like this:
	 *
	 * <% loop LanguageChooser %>
	 *   $Title, $Current, and any other vars in your page instance
	 * <% end_loop %>
	 *
	 * @return DataList
	 */
	public function LanguageChooser() {
		if (!Controller::curr()) {
			return;
		}
		if ($langs = Translatable::get_allowed_locales()) {
			$data = ArrayList::create();
			foreach($langs as $key => $code) {
				if ($code == Translatable::get_current_locale()) {
					$this->owner->Current = 'current';
					$data->push($this->owner);
				} else {
					$translation = $this->owner->getTranslation($code);
					if ($translation) {
						$data->push($translation);
					} else {
						$page = Translatable::get_one_by_locale("SiteTree", $code,"URLSegment LIKE 'home%'");
						if ($page) {
							$data->push($page);
						}
					}
				}
			}
			return $data;
		}
		return false;
	}
}
