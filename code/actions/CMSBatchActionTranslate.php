<?php

/**
 * Publish items batch action.
 *
 * @package lange
 * @subpackage batchaction
 */
class CMSBatchActionTranslate extends CMSBatchAction {
	function getActionTitle() {
		return _t('CMSBatchActions.TRANSLATE_PAGES_TO_DRAFT', 'Translate to draft');
	}

	function run(SS_List $pages) {
		$status = array('translated' => array(), 'error' => array());
		
		foreach ($pages as $page) {
			$id = $page -> ID;

			foreach (Translatable::get_allowed_locales() as $locale) {
				if ($page -> Locale == $locale)
					continue;
				if (!$page -> hasTranslation($locale)) {
					try {
						$translation = $page -> createTranslation($locale);
						$successMessage = $this -> duplicateRelations($page, $translation);
						$status['translated'][$translation->ID] = array(
			 				'TreeTitle' => $translation->TreeTitle,
			 			);
						$translation -> destroy();
						unset($translation);
					} catch (Exception $e) {
						// no permission - fail gracefully
						$status['error'][$page->ID] = true;
					}
				}
			}

			$page -> destroy();
			unset($page);
		}

		return $this -> response(_t('CMSBatchActions.TRANSLATED_PAGES', 'Translated %d pages to draft site, %d failures'), $status);
	}

	function applicablePages($ids) {
		return $this -> applicablePagesHelper($ids, 'canPublish', true, false);
	}

	public function duplicateRelations($obj, $new) {
		if ($has_manys = $obj -> has_many()) {
			foreach ($has_manys as $name => $class) {
				if ($related_objects = $obj -> $name()) {
					// Debug::dump($related_objects);
					foreach ($related_objects as $related_obj) {
						$o = $related_obj -> duplicate(true);
						$new -> $name() -> add($o);
					}
				}
			}
		}
		if ($many_manys = $obj -> many_many()) {
			foreach ($many_manys as $name => $class) {
				if ($obj -> $name()) {
					$new -> $name() -> setByIdList($obj -> $name() -> column());
				}
			}
			$new -> write();
		}
	}

}
