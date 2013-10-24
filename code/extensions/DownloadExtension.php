<?php
/**
 * Controller extension to force a direct download of a file. Works out of the box using the CleanFile.DownloadLink.
 *
 * Add this extension to a Controller instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('Page_Controller', 'DownloadExtension');
 *
 * @package cleanutilities
 * @subpackage extensions
 *
 * @author arillo
 */
class DownloadExtension extends Extension {

	static $allowed_actions = array(
		'download'
	);

	/**
	 * Controller action for downloading a Clean File.
	 * It responds to urls matching the following pattern:
	 * 
	 * /download/ClassName/ID
	 * 
	 * like:
	 * 
	 * /download/CleanFile/1
	 * /download/CleanImage/1
	 *
	 * @param $request
	 * @return SS_HTTPRequest
	 */
	function download($request) {
		$classname = Convert::raw2sql($request->latestParam('ID'));
		$id = Convert::raw2sql($request->latestParam('OtherID'));
		if (is_numeric($id) && $classname!='' && $id!='') {
			if ($file = DataObject::get_by_id($classname, $id)) {
				if ($file->AttachmentID != 0 && isset($file->AttachmentID)) {
					return SS_HTTPRequest::send_file(
						file_get_contents($file->Attachment()->getFullPath()),
						$file->Attachment()->Name
					);
				}
			}
		}
		return $this->owner->redirect($this->owner->Link());
	}
}