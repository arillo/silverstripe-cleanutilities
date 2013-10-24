#Clean Utils
__written by Arillo__

Utility functions. Some of them are required by other classes in this module.

##CleanGD
Provides extra funtionality to GD

###Install

	// add the extensions in _config.php
	Object::add_extension('GD','CleanGD');

###Public functions

#####imagetint(&$img, $tint_r = 255, $tint_g = 255, $tint_b = 255)
	/**
	 * Tints an image.
	 * 
	 * @param data $img
	 * @param int $tint_r
	 * @param int $tint_g
	 * @param int $tint_b
	 * @return data
	 * 
	 * @example:
	 * imagetint($img);  // Grayscale, no tinting
	 * imagetint($img, 304, 242, 209);  // What I use for sepia
	 * imagetint($img, 0, 0, 255);  // A berry blue image
	 * 
	 * The RGB values for tinting are normally from 0 to 255.
	 * But, you can use values larger than 255 to lighten and "burn" the image.
	 * The sepia example above does this a little, the below example provides
	 * a better example of lightening the image and burning the light areas
	 * out a little:
	 * 
	 * imagetint($img, 400, 400, 400);  // Lighten image
	 * imagetint($img, 127, 127, 127);  // Darken image
	 */
	function imagetint(&$img, $tint_r = 255, $tint_g = 255, $tint_b = 255)

### Usage

This class is used internally in ImageDataExtension. So to colorize an image please use ImageDataExtension. We will do somthing like:

	$Image.ColorizeImage(255,0,0)


##CleanUtils
Provides a couple of helper methods for Theme handling and lets us set a Template other than the default on this page instance.

###Public static variables

#####$module = "cleanutilities"
	/**
	 * Define the foldername for this module.
	 * @var string
	 */
	public static $module = "cleanutilities";

#####$folder_max_files = 100
	/**
	 * Max. file count in controlled upload folders.
	 * 
	 * @var int
	 */
	public static $folder_max_files = 100;
###Public static functions

#####add_required_css($form, $cssClass = "required" )
	/**
	 * Helper function, which adds the given $cssClass to all
	 * $form fields specified by its requiredfields
	 *
	 * @param Form $form
	 * @param string $cssClass
	 */
	public static function add_required_css(Form $form, $cssClass = "required")
	
#####instance_of($class, $parentClass)
	/**
	 * Like PHPs instance_of but the SS way of doing it.
	 *
	 * @param string $class
	 * @param string $parentClass
	 * @return bool
	 */
	public static function instance_of($class, $parentClass)


#####generate_urlsegment($title)
	/**
	 * Generates an url friendly representation of a given string.
	 *
	 * @param string $title
	 * @return string
	 */
	public static function generate_urlsegment($title)

#####setup_locale($locale)
	/**
	 * Sets i18n locale and adds Content-language to meta tags.
	 * @param string $locale
	 */
	public static function setup_locale($locale)

#####controlled_upload_folder($foldername = 'customfiles')
	/**
	 * Limits the count of files in a folder to $folder_max_files.
	 * Automatically adds new subfolders.
	 * 
	 * @param string $foldername
	 * @return string
	 */
	public static function controlled_upload_folder($foldername = 'customfiles')

#####create_gridfield_for($model, $relationname, $reference)
	/**
	 * A simple Gridfield factory
	 * @param  string $model
	 * @param  string $relationname
	 * @param  DataObject $reference
	 * @return GridField
	 */
	 public static function create_gridfield_for($model, $relationname, $reference)

<!---
##EmailSender
Helper for sending emails with ease.

###Public static functions
#####public static function send_email($from = '', $to = '',  $subject = '', $template = '', $popdata = null, $file = null)
	/**
	 * Send an email with the give parameters.
	 *
	 * @param string $from
	 * @param string|array $to
	 * @param string $subject
	 * @param string $template
	 * @param array $popdata
	 * @param File $file
	 * @return bool
	 */
	public static function send_email($from = '', $to = '',  $subject = '', $template = '', $popdata = null, $file = null)


#####public static function validate_email($email = '')

	/**
	 * Email address validity check.
	 *
	 * @param string $email
	 * @return bool
	 */
	public static function validate_email($email = '')


###Usage

	// $from gets an Email address
	$from = 'you@domain.net';

	// $to can be an email, an array of emails or the name of a group. Respectively:

	$to = 'someone@domain.net';
	$to = array('someone@domain.net', 'someoneelse@domain.net');
	$to = 'Administrators';

	// $subject sets the subject of the email:
	$subject = 'this is a test';

	// $template defines the template to use for rendering the email:
	$template = 'TestEmail'

	// $popdata can get the instance of a DataObject, an indexed array or a string. Respectively:
	// considering we have a $dataonject variable.
	$dataobject = new DataObject();
	// or
	$dataObject = DataObject::get("PageComments");

	$popdata = $dataobject;
	$popdata = array(
		'Object' => $dataobject,
		'Title' => 'Information for our dataobject'
	);
	$popdata = 'email content here';
	// $file allows us to send an attached file with the email
	$file = DataObject::get_one('File');

	// an then fire it
	EmailSender::send_email($from,$to,$subject,$template,$popdata,$file)

###Template usage
in Email/TestEmail.ss

	// for $popdata = $dataobject
	<% control Data %>
		$dataobject_variables...
	<% end_control %>

	// for $popdata = array('Object' => $dataobject,'Title' => 'Information for our dataobject');
	<% control Data %>
		<h2>$Title</h2>
		<% control Object %>
			$dataobject_variables...
		<% end_control %>
	<% end_control %>

	// for $popdata = 'email content here';
	<div>$Data</div>

__Note__: For validating purposes the function will return a boolean indicating if the email(s) were succesfully sent.
-->
##VideoUtility
Provides helper funtionality to handle and display videos from diffrent media platforms. Supported platforms are:

 * Youtube
 * Vimeo
 * Metacafe
 * Dailymotion
 * Facebook

###Public static functions
#####validate_video($media_url)
	/**
	 * Checks if the given $media_url is a playable one.
	 *
	 * @param string $media_url
	 * @return bool
	 */
	static function validate_video($media_url)


#####video_embed($media_url, $width = 400, $height = 300, $autoplay = false)

	/**
	 * Generates the fiting embed code for the video
	 * according to its service.
	 *
	 * @param string $media_url
	 * @param int $width
	 * @param int $height
	 * @param bool $autoplay
	 * @return string
	 */
	static function video_embed($media_url, $width = 400, $height = 300, $autoplay = false)

#####is_youtube($media)

	/**
	 * Tests if a given url is a youtube video url.
	 *
	 * @param string $media
	 * @return bool
	 */
	static function is_youtube($media)

#####prepare_url($media)

	/**
	 * Splits the given $media url into its logical parts.
	 *
	 * @param string $media
	 * @return array
	 */
	static function prepare_url($media)


