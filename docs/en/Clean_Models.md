#Clean Models
__written by Arillo__

All models in this module are mainly used by its corresponding [Model Decorators](Model_Decorators). They are mostly wrappers for File (sub-)classes and they add a relation to the belonging page in a one-to-many fashion.

	// in subclass of a CleanModel extend form´s fieldset like this:
	public function updateCMSFields_forPopup(FieldSet &fields)



##CleanFile
Wrapper for a File.

####db

		'Title'=> 'Text'

####has_one

		'Attachment' => 'File',
		'Reference' => 'SiteTree'

###Public functions
#####DownloadLink()
	/**
	 * Returns a link like URLSegment/download/ClassName/ID.
	 * To make this to work you need to implement a "download" function in
	 * the Reference's controller.
	 *
	 * @return mixed
	 */
	public function DownloadLink()
#####AbsoluteDownloadLink()
	/**
	 * Returns a absolute download link like:
	 * http://domain.com/URLSegment/download/ClassName/ID
	 * 
	 * To make this to work you need to implement a "download" function in
	 * the Reference's controller.
	 * This can be achieved by using DownloadExtension.
	 * 
	 * @return string
	 */

Tip: You can use DownloadExtension to provide the download function to your controller.

##CleanImage
Wrapper for an Image.
####db

		'Title'=> 'Text'

####has_one

		'Attachment' => 'File',
		'Reference' => 'SiteTree'

###Public functions
#####getThumbnail()
	/**
	 * Returns the CMS thumbnail, if an image is attached.
	 * Mainly used by DataObjectManager.
	 *
	 * @return mixed
	 */
	function getThumbnail()


#####DownloadLink()
	/**
	 * Returns a relative link like URLSegment/download/ClassName/ID.
	 * To make this to work you need to implement a "download" function in
	 * the Reference's controller.
	 *
	 * @return string
	 */
	public function DownloadLink()

#####AbsoluteDownloadLink()

	/**
	 * Returns a absolute download link like:
	 * http://domain.com/URLSegment/download/ClassName/ID
	 * 
	 * To make this to work you need to implement a "download" function in
	 * the Reference's controller.
	 * This can be achieved by using DownloadExtension.
	 * 
	 * @return string
	 */
	public function AbsoluteDownloadLink()


##CleanLink
Wrapper for external links.
####db

		'Title' => 'Text',
		'URL' => 'Varchar(255)',
		'Target'	=> "Enum('_self,_blank','_self')"

####has_one

		'Reference' => 'SiteTree'

##CleanTeaser
Wrapper for a Teaser. A DataObject that gives you two text fields, an Image, a relation to an internal Page (RelatedPage) and many further Links [CleanTeaserLink](Clean_Models#cleanteaserlink).
####db

		'Title'=> 'Text',
		'Description' => 'HTMLText'

####has_one

		'RelatedPage' => 'SiteTree',
		'Reference' => 'SiteTree',
		'Image' => 'Image'

####has_many

		'Links' => 'CleanTeaserLink'

###Public functions
#####getThumbnail()
	/**
	 * Returns the CMS thumbnail, if an image is attached.
	 * Mainly used by DataObjectManager.
	 *
	 * @return mixed
	 */
	function getThumbnail()

##CleanTeaserLink
Link used in [CleanTeaser](Clean_Models#cleanteaser).
####db

		'Title'=> 'Text',
		'URL' => 'Text',
		'Type'	=> "Enum('_self,_blank','_blank')"

####has_one

		'Reference' => 'CleanTeaser'

##CleanVideo
A DataObject for Videos. Provides a video that can either be embedded through a service api or through its own embed code.

####db

		"Title" => "Text",
		"VideoAddress" => "Text",
		"VideoType" => "Enum('Embed, File','Embed')",
		"Autoplay" => "Boolean"

####has_one

		'Reference' => 'SiteTree',
		'VideoFile' => 'File'


###Public functions
#####getValidator()
	/**
	 * Returns custom validator
	 *
	 * @return CleanVideo_Validator
	 */
	public function getValidator()

#####VideoEmbed($width = 640, $height = 375, $autoplay = null)

	/**
	 * Returns the actual video embed code.
	 *
	 * @param int $width
	 * @param int $height
	 * @return string
	 */
	 public function VideoEmbed($width = 640, $height = 375)


#####getVideoFileName()
	/**
	 * Getter for the video file name
	 *
	 * @return string
	 */
	public function getVideoFileName()
#####VideoID()

	 /**
	 * Getter for the service's video id.
	 *
	 * @return string
	 */
	public function VideoID()
##CleanVideo_Validator
CleanVideo also comes with it´s own validator class.
