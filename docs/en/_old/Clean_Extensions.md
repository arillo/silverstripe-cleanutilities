#Clean Extensions
__written by Arillo__

Extensions for controllers which can be used together with some Clean models or model decorators.

##DownloadExtension
Controller extension to force a direct download of a file. Works out of the box using the ([Clean Models](Clean_Models.md)).DownloadLink.
###Install

	// add the extensions in _config.php
	Object::add_extension('Page_Controller', 'DownloadExtension');

###Public functions
#####download($request)
	/**
	 * Controller action for sending the download file.
	 *
	 * @param SS_HTTPRequest $request
	 * @return SS_HTTPResponse
	 */
	function download($request)
##ThemeExtension
Provides a couple of helper methods for Theme handling and lets us set a Template other than the default on this page instance. Works together with [ThemeDecorator](Utilities_Decorators#themedecorator).
###Install

	// add the extensions in _config.php
	Object::add_extension('Page', 'ThemeDecorator');
	Object::add_extension('Page_Controller', 'ThemeExtension');


###Public functions
#####index()
	/**
	 * Renders the decorated page with a given template.
	 * @return array
	 */
	function index()

