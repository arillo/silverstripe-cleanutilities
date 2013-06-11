#Utilities DataExtensions
__written by Arillo__

##CMSPublishableDataExtension
Provides publish/unpublish functionality to DataObjects.

###Install
	// in _config.php
	Object::add_extension('CleanFile', 'CMSPublishableDataExtension');

###Public functions
#####getPublishStatus()

	/**
	 * Returns an inactive Checkbox,
	 * usefull feature for GridField etc.
	 *
	 * @return string
	 */
	public function getPublishStatus()

When an object gets extended by CMSPublishableDataExtension we need to realize that probably we will use a GridField to manage the different it. By using our custom [gridfield generator](Clean_Utils.md#create_gridfield_for) you won't need to worry about adding the published status into your gridfield columns, nevertheless here is how you can add it manually.
	
	$gridfield = GridField::create();
	$datacolumns = $gridfield->getConfig()->getComponentByType('GridFieldDataColumns');
	$cfields = singleton($model)->summaryFields();
	if(singleton($model)->hasExtension('CMSPublishableDataExtension') && !isset($cfields['PublishStatus'])){
		$cfields = array('PublishStatus' => 'PublishStatus') + $cfields;
	}
	$datacolumns->setDisplayFields($cfields);
	
##ControlledFolderDataExtension

Provides the extended class with the ability
to use controlled upload folders. Controlled in this case means,
the amount of files contained in each folder is limited to the value of
ControlledFolderDataExtension::$folder_max_files. It will create subfolders
named in a numeric way like 000000 to 999999.

Add this extension to a File instance by adding this to your _config.php:

	// For Clean Models this is already done in the modules _config.php
	DataObject::add_extension('CleanFile', 'ControlledFolderDataExtension');

 With this configuration, ControlledFolderDataExtension::$folder_max_files will be used as limit and the folder will be named by the related class name ("CleanFile") in this example.
 
You also can pass a config array to this method to override the defaults, like this:

	ControlledFolderDataExtension::set_controlled_folder_for(
		 "CleanFile",
		array(
			'folderName' => "MyFolder",
			'folderMaxFiles' => 23
	));
 
This extension adds an instance function to the decorated class e.g.
	$cleanFile = CleanFile::create();
	// for using a controlled upload folder with default/ earlier created settings
	$cleanFile->getUploadFolder();
	// you can also pass a config object like:
	$cleanFile->getUploadFolder(
		array(
			'folderName' => "MyFolder",
			'folderMaxFiles' => 23
		),
		true // flag for, if this config should be made permanent for later use.
	);
 
###Install
Add this extension to a File instance by adding this to your _config.php:

	DataObject::add_extension(
		'YourObject',
		'ControlledFolderDataExtension'
	);
	ControlledFolderDataExtension::set_controlled_folder_for(
		"YourObject",
		"FolderName",
		100
	);

This extension adds an instance function to the decorated class e.g.
	
	$cleanFile = CleanFile::create();
	
	// for using a controlled upload folder with default settings
	$cleanFile->getUploadFolder();
	
	// you can also pass a config object as following:
	$cleanFile->getUploadFolder(
		array(
			'folderName' => "MyFolder",
			'folderMaxFiles' => 23
		),
		true // flag for saving this config for later use.
	);

###Public static functions
#####get_folder_config($config = null)

	/**
	 * Generates a folder config with default values.
	 * The defaults can be modified by given $config.
	 * To make this work $config should be an array with a pattern like
	 * array(
	 *	'folderName' => 'SomeName',
	 *	'folderMaxFiles' => 100
	 * );
	 * 
	 * @param  array $config
	 * @return array
	 */
	public static function get_folder_config($config = null)
	
#####set_controlled_folder_for($className, $config)
	/**
	 * Sets up a controlled upload folder [$folderName]
	 * for a class [$className].
	 * Limits the count of files on each folder to
	 * 
	 * @param  string $className
	 * @param  string|array $config
	 */
	 public static function set_controlled_folder_for($className, $config)
	
#####find_or_make_controlled_folder($config)
	/**
	 * Limits the count of files in a folder to $folder_max_files.
	 * Automatically adds new subfolders.
	 * 
	 * @param string|array $config
	 * @return string
	 */
	public static function find_or_make_controlled_folder($config)
	
#####sanitize_folder_name($foldername)
	/**
	 * Folder name sanitizer.
	 * Checks for valid names and sanitizes
	 * against directory traversal.
	 * 
	 * @param  string $foldername [description]
	 * @return string
	 */
	public static function sanitize_folder_name($foldername)
#####getUploadFolder($config = null, $makePermanent = false)

	/**
	 * Getter for the actual folder name.
	 * If an $config array is passed, it will return 
	 * a controlled folder with this configuration.
	 * It will also return controlled folders if they are setup by
	 * ControlledFolderDataExtension::set_controlled_folder_for.
	 * If $makePermanent is used, it will register this configuration
	 * for later use.
	 * 
	 * @param  array $config
	 * @param  array $makePermanent
	 * @return string
	 */
	public function getUploadFolder($config = null, $makePermanent = false)

##FileDataExtension
Provides extra functionality to File.
###Install
	// in _config.php
	Object::add_extension('File', 'FileDataExtension');
###Public functions
#####appCategory()
	/**
	 * Returns the application category this file belongs to.
	 *
	 * @return string
	 */
	public function appCategory()
#####FileCreationDate()
	/**
	 * Reads and returns the creation date of the file.
	 *
	 * @return Date
	 */
	public function FileCreationDate()
##FolderDataExtension
Provides extra functionality to Folder.
###Install
	// in _config.php
	Object::add_extension('Folder', 'FolderDataExtension');

###Public functions
#####getSortedChildFolders($sortField = 'Title', $sortDir = 'ASC')
	/**
	 * Get the children sorted by name of this folder that are also folders.
	 * 
	 * @return ArrayList
	 */
	function getSortedChildFolders($sortField = 'Title', $sortDir = 'ASC')
#####getSortedChildren($sortField = 'Title', $sortDir = 'ASC')
	/**
	 * All subfolders sorted by $sort.
	 * 
	 *  
	 * @param $sort
	 * @return DataList
	 */
	public function getSortedChildren($sortField = 'Title', $sortDir = 'ASC')
##GroupLoginDataExtension
Provides extra fields to Group, to make custom redirects after login possible. Works together with CustomRedirectLoginForm.
###Install
	// in _config.php
	Object::useCustomClass('MemberLoginForm', 'CustomRedirectLoginForm');
	Object::add_extension('Group', 'GroupLoginDataExtension');
###db
	"GoToAdmin" => "Boolean"
###has_one
	"LinkPage" => "SiteTree"

##ImageDataExtension
Provides extra functionality to Image classes.
###Install
	// in _config.php
	Object::add_extension('Image', 'ImageDataExtension');
###Public functions
#####getHalfWidth()
	/**
	 * Returns half of image width.
	 *
	 * @return int
	 */
	function getHalfWidth()
#####SetMaxRatioSize($width, $height)
	/**
	 * @param int $width
	 * @param int $height
	 * @return Image
	 */
	public function SetMaxRatioSize($width, $height)
#####SetMaxWidth($width)
	/**
	 * @param int $width
	 * @return Image
	 */
	function SetMaxWidth($width)
#####ColorizeImage($tint_r = 255, $tint_g = 255, $tint_b = 255)
	/**
	 * Created a tinted version of this image.
	 *
	 * @param int $tint_r
	 * @param int $tint_g
	 * @param int $tint_b
	 * @return Image
	 */
	function ColorizeImage($tint_r = 255, $tint_g = 255, $tint_b = 255)
#####PaddedImageWithColor($width, $height, $color)
	/**
	 * Generates an image with colored padding.
	 *
	 * @param int $width
	 * @param int $height
	 * @param string $color hex color like FF0000
	 * @return Image
	 */
	 public function PaddedImageWithColor($width, $height, $color)
#####RotatedImage($angle)
	/**
	 * Rotate an image by angle.
	 *
	 * @param int $angle
	 * @return Image
	 */
	 public function RotatedImage($angle)
##LanguageDataExtension
Provides SiteTree classes with a language menu.
###Install
	// in _config.php
	Object::add_extension('Page', 'LanguageDataExtension');
###Public functions
#####LanguageChooser()

	/**
	 * Returns a DataObjectSet containing Pages.
	 * The provided links point to their translated pages.
	 * You can use it in templates like this:
	 *
	 * <% loop LanguageChooser %>
	 *   $Title, $Current, and any other vars in your page instance
	 * <% end_loop %>
	 *
	 * @return DataList
	 */
	public function LanguageChooser()

###Template usage

	<div id="Lang">
		<% loop LanguageChooser %>
			<a href="$Link" class="lang $Current">
				<% if Locale = de_DE %>DE<% end_if %>
				<% if Locale = en_US %>EN<% end_if %>
			</a>
		<% end_loop %>
	</div>

##ModuloDataExtension
Provides with some modulo helper functionality for DataObjects when they are within a DataObjectSet.
###Install
	// in _config.php
	Object::add_extension('DataObject', 'ModuloDataExtension');
###Template usage
	<% loop Teasers %>
		$GetModulo($Pos, 2)
		$GetBeforeModulo($Pos, 2)
	<% end_loop %>

###Public functions
#####GetModulo($pos, $modulo)
	/**
	 * Indicator for, if this object is the nth child of a collection.
	 *
	 * @param int $pos current position in the list
	 * @param int $modulo
	 * @return bool
	 */
	public function GetModulo($pos, $modulo)
#####GetBeforeModulo($pos, $modulo)
	/**
	 * Indicator for, if this object is before the nth child of a collection.
	 *
	 * @param int $pos current position in the list
	 * @param int $modulo
	 * @return bool
	 */
	public function GetBeforeModulo($pos, $modulo)
#####LessThan($pos, $num)
	/**
	 * Tests if current position is smaller than a 
	 * given value.
	 * 
	 * @param int $pos current position in the list
	 * @param int $num
	 * @return bool
	 */
	public function LessThan($pos, $num)


##NotificationDataExtension
Provides notification/flash messages to SiteTree classes.
###Install
	// in _config.php
	Object::add_extension('Page', 'NotificationDataExtension');
###Public static functions
#####set_session_message($str = '', $mode = 'msgGood')

	/**
	 * Adds a message $str to notifications with a certain $mode.
	 * This static version of this function makes this usable from everywhere.
	 *
	 * @param string $str
	 * @param string $mode
	 */
	public static function set_session_message($str = '', $mode = 'msgGood')
###Public functions
#####HaveMessages()

	/**
	 * Indicates if current notifications do exist.
	 *
	 * @return bool
	 */
	public function HaveMessages()
#####SessionMessages()

	/**
	 * Returns all current notifications.
	 *
	 * @return DataObjectSet
	 */
	public function SessionMessages()

###Template usage
e.g. in Page.ss

	<% include Notifications %>

in Includes/Notifications.ss

	<% if HaveMessages %>
		<div class="notifications">
			<% loop SessionMessages %>
				<p class="$Mode">$Msg</p>
			<% end_loop %>
		</div>
	<% end_if %>
##PublishDateDataExtension
Provides a Datetime entry for pages, so we can sort them or do other actions based on a date. If Publish Date is not set when creating a page PublishDate will be set automatically to the Date and time of save and publishing.

###db

	'PublishDate' => 'Datetime'

###Install
	// in _config.php
	Object::add_extension('My-Page','PublishDateDataExtension');

##SecondMenuDecorator
Provides SiteTree with an extra menu.
Adds a checkbox to Settings/Visability tab in CMS.
###Install
	// in _config.php
	Object::add_extension('SiteTree', 'SecondMenuDataExtension');
###db
	'SecondMenu' => 'Boolean'
###Public functions
#####SecondMenu($parent = 0)

	/**
	 * Returns all SiteTree instances which have SecondMenu activated.
	 *
	 * @param int $parent
	 * @return DataObjectSet
	 */
	public function SecondMenu($parent = 0)
###Template usage
e.g. in Page.ss

	<% include SecondMenu %>
Includes/SecondMenu.ss

	<ul id="Menu">
		<% loop SecondMenu %>
			<li>
				<a href="$Link" title="$Title.XML" class="$LinkingMode">$MenuTitle.XML</a>
			</li>
		<% end_loop %>
	</ul>

##SiteConfigAddressExtension

Generates general contact data which can be filled by the website customer.

###Install
	// in _config.php
	Object::add_extension('SiteConfig', 'SiteConfigAddressExtension');

###db:

	'Company' => 'Text',
	'Address' => 'Text',
	'Country' => 'Text',
	'ZIP' => 'Text',
	'City' => 'Text',
	'Telephone' => 'Text',
	'Cell' => 'Text',
	'Fax' => 'Text',
	'Email' => 'Text'

###Template usage:

	$SiteConfig.Company

##SiteConfigExtension
Provides SiteConfig with extra fields and adds Google Analytics to your site.
###Install
	// in _config.php
	Object::add_extension('SiteConfig', 'SiteConfigExtension');

###db:

	'Copyright' => 'Text'
	'GoogleAnalyticsTrackingCode' => 'Text'

###Public functions
#####GoogleAnalytics()

	/**
	 * Adds Google Analytics javascript to your template
	 */
	public function GoogleAnalytics()

###Template usage
e.g. in <head> section of main Page.ss you can add:

	$SiteConfig.GoogleAnalytics
##SortableDataExtension
Provides a DataObject with a SortOrder field. Usefull for GridField sorting. All Clean models use this extension by default.
###Install
	// in _config.php
	Object::add_extension('CleanFile', 'SortableDataExtension');

###db:

	'SortOrder' => 'Int'

##TextDataExtension
Provides a couple of helper methods to Text classes.
###Install
	// in _config.php
	Object::add_extension('HTMLText', 'TextDataExtension');

###Public functions
#####SummaryHTML($limit = 100, $add = "&hellip;")
	/**
	 * Shortens (html) text to a given $limit and appends $add to it.
	 *
	 * @param int $limit
	 * @param string $add
	 * @return string
	 */
	public function SummaryHTML($limit = 100, $add = "&hellip;")
#####ConvertPlainTextToUTF8($limit = 0)
	/**
	 * Converts a given text into uft8 and shortens ist to $limit.
	 * Caution: Dont't use it with HTMLText instances.
	 * 
	 * @param int $limit
	 * @return string
	 */
	public function ConvertPlainTextToUTF8($limit = 0)
#####LimitPlainTextToUTF8($limit = 20, $add = "...")
	/**
	 * Converts a given text into uft8 and 
	 * shortens it by $limit and adds $add.
	 * Caution: Dont't use it with HTMLText instances.
	 * 
	 * @param int $limit
	 * @param string $add
	 * @return string
	 */
	public function LimitPlainTextToUTF8($limit = 20, $add = "...")
#####EmailObfuscated()
	/**
	 * Returns a representation of this text
	 * with all email addresses converted into html character entities.
	 *
	 * @return string
	 */
	public function EmailObfuscated()
#####MoreWordsThan($numWords = 23)
	/**
	 * Tests if the text is longer than $numWords.
	 * 
	 * @param int $numWords
	 * @return bool
	 */
	function MoreWordsThan($numWords = 23)
###Template usage
	$Content.SummaryHTML(20)
	$Content.ConvertText(20)
	$Content.LimitCharactersUTF(20, "&rarr;")
	$Content.EmailObfuscated

	<% if Content.CheckWordCount(12) %>
		$Content.SummaryHTML(12)
	<% else %>
		$Content
	<% end_if %>


##ThemeDataExtension

Provides custom template choosing functionality, which lets you set a Template to a page. Works together with [ThemeExtension](Clean_Extensions.md#themeextension).

###Install
	// in _config.php
	Object::add_extension('Page', 'ThemeDataExtension'); 
	Object::add_extension('Page_Controller', 'ThemeExtension');

###db
	'Template'	=> 'Varchar'
###Public functions
#####ThemeDir()
	/**
	 * Returns a relative path to current theme directory.
	 *
	 * @return mixed
	 */
	function ThemeDir()
#####TemplateFile()
	/**
	 * Returns a relative path to current template file.
	 *
	 * @return string
	 */
	function TemplateFile()
#####TemplateAbsFile()
	/**
	 * Returns a absolute path to current template file.
	 *
	 * @return string
	 */
	function TemplateAbsFile()
#####TemplateDir($directory = 'Layout/')
	/**
	 * Returns the current template directory.
	 *
	 * @param string $directory
	 * @return string
	 */
	function TemplateDir($directory = 'Layout/')
#####getSelectableTemplates($directory = 'Layout/')
	/**
	 * Returns an array of all selectable template files.
	 *
	 * @param string $directory
	 * @return array
	 */
	public function getSelectableTemplates($directory = 'Layout/')

##UtilitiesDataExtension
Provides a couple of helper methods to the SiteTree instances.

###Install
	// in _config.php
	Object::add_extension('Page', 'UtilitiesDataExtension');

###Public functions
#####MemberGroup($ID = 1)
	/**
	 * Returns all members of a group by ID .
	 *
	 * @param int $ID  group ID
	 * @return DataObjectSet
	 */
	public function MemberGroup($ID = 1)
#####PageInstance($pagetype = 'Page')
	/**
	 * Returns a SiteTree instance by ClassName.
	 *
	 * @param string $pagetype
	 * @return mixed DataObject|bool
	 */
	public function PageInstance($pagetype = 'Page')
#####PageControllerInstance($pagetype = 'Page')
	/**
	 * Return a Page_Controller instance by page ClassName.
	 *
	 * @return mixed Page_Controller|bool
	 */
	public function PageControllerInstance($pagetype = 'Page')
#####ShortLang()
	/**
	 * Returns shortlang from Locale.
	 *
	 * @return string
	 */
	public function ShortLang()
###Usage
For a good locale behaviour we can introduce it in Page_Controller::init() function.

	// in Page_controller
	public function init(){
		parent::init();
		// set up locale
		UtilitiesDecorator::setupLocale($this->Locale);
		// you also can do it like this
		// $this->setupLocale($this->Locale);
	}
###Template usage
	<% with MemberGroup(1) %>
		<!-- Will print all ADMIN members here… -->
		$Email $FullName
	<% end_with %>

	<% with PageInstance(SomePageClass) %>
		<!-- All public page stuff available here… -->
		$Title, etc...
	<% end_with %>

	<% with PageControllerInstance(SomePageClass) %>
		<!-- All public controller stuff available here... -->
		$Form
	<% end_with %>

	$ShortLang <!-- DE -->

