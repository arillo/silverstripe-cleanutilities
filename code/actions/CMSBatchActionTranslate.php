<?php
/**
 * Translate items to all available locales and store new pages as draft - CMS batch action.
 * Requires {@link Translatable::enabled} in your _config.php.
 *
 * Add batch actions by adding this to your _config.php:
 * CMSBatchActionHandler::register('translate', 'CMSBatchActionTranslate');
 *
 * @author Dirk Adler / KLITSCHE.DE
 *
 * remastered for copying relations
 */
class CMSBatchActionTranslate extends CMSBatchAction
{
	function getActionTitle() {
		return _t('CMSBatchActions.TRANSLATE_PAGES_TO_DRAFT', 'Translate to draft');
	}

	function getDoingText() {
		return _t('CMSBatchActions.TRANSLATING_PAGES_TO_DRAFT', 'Translating pages');
	}

	function run(DataObjectSet $pages) {
		return $this->batchaction(
			$pages,
			null,
			_t('CMSBatchActions.TRANSLATED_PAGES_TO_DRAFT', 'Processed %d pages and saved %d translations (draft)')
		);
	}
	public function duplicateRelations($obj, $new) {
		if($has_manys = $obj->has_many()) {
			foreach($has_manys as $name => $class) {
				if($related_objects = $obj->$name()) {
					foreach($related_objects as $related_obj) {
						$o = $related_obj->duplicate(true);
						$new->$name()->add($o);
					}
				}
			}
		}
		if($many_manys = $obj->many_many()) {
		foreach($many_manys as $name => $class) {
			if($obj->$name()) {
					$new->$name()->setByIdList($obj->$name()->column());
				}
			}
			$new->write();
		}
	}
	public function batchaction(DataObjectSet $pages, $helperMethod, $successMessage) {
		if (Translatable::get_allowed_locales() == null) {
			FormResponse::add('statusMessage("'._t('CMSBatchAction.TRANSLATE_ALLOWED_LOCALES','Please add Translatable::set_allowed locales to your _config.php').'","bad");');
		} else {
			$translated = 0;
			foreach($pages as $page) {
				foreach (Translatable::get_allowed_locales() as $locale) {
					if ($page->Locale == $locale) continue;
					if (! $page->hasTranslation($locale)) {
						try {
							$translation = $page->createTranslation($locale);
							$successMessage = $this->duplicateRelations($page, $translation);
							if ($helperMethod) $translation->$helperMethod();
							$translation->destroy();
							unset($translation);
							$translated++;
						}
						catch (Exception $e) {
							// no permission - fail gracefully
						}
					}
				}
				$page->destroy();
				unset($page);
			}
			$message = sprintf($successMessage, $pages->Count(), $translated);
			FormResponse::add('statusMessage("'.$message.'","good");');
		}
		return FormResponse::respond();
	}
}

/**
 * Translate and publish items to all other available locales - batch action.
 * Requires {@link Translatable::enabled} in your _config.php.
 *
 * Add batch actions by adding this to your _config.php:
 * CMSBatchActionHandler::register('translate-and-publish', 'CMSBatchActionTranslateAndPublish');
 *
 * @author Dirk Adler / KLITSCHE.DE
 */
class CMSBatchActionTranslateAndPublish extends CMSBatchActionTranslate {
	function getActionTitle() {
		return _t('CMSBatchActions.TRANSLATE_PAGES_TO_LIVE', 'Translate and publish');
	}
	function getDoingText() {
		return _t('CMSBatchActions.TRANSLATING_PAGES_TO_LIVE', 'Translating and publishing pages');
	}
	function run(DataObjectSet $pages) {
		return $this->batchaction(
			$pages,
			'doPublish',
			_t('CMSBatchActions.TRANSLATED_PAGES_TO_LIVE', 'Processed %s pages and saved %s translations (live)')
		);
	}
}
