#Clean Actions

Extra functionalty for working with forms. Partly from third parties.

##CMSBatchActionTranslate
Translate items to all available locales and store new pages as draft - CMS batch action.
Requires {@link Translatable::enabled} in your _config.php.

__by: M. Dirk Adler / KLITSCHE.DE__

###Install
	// in _config.php
	CMSBatchActionHandler::register('translate', 'CMSBatchActionTranslate');
	CMSBatchActionHandler::register('translate-and-publish','CMSBatchActionTranslateAndPublish');

