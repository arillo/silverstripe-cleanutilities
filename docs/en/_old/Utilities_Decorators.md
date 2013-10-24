#Utilities Decorators
__written by Arillo__

##AssetsDecorator (utils)
Provides extra functionality to all kind of assets, mainly File (sub-)classes.
###Install
	// in _config.php
	Object::add_extension('File', 'AssetsDecorator');

###Public static functions
#####ControlledUploadFolder($foldername = '/uploads/')
	/**
	 * Limits the count of files in a folder to AssetsDecorator::$maxfilesperfolder.
	 * Automatically adds new subfolders.
	 *
	 * @param string $foldername
	 * @return string
	 */
	public static function ControlledUploadFolder($foldername = '/uploads/')

	// in _config.php
	AssetsDecorator::$maxfilesperfolder = 200

###Public functions
#####HomeDirectory()
	/**
	 * A folder name compund [$ClassName] / [$ID]
	 *
	 * @return string
	 */
	public function HomeDirectory()

##CMSPublishableDecorator
Provides publish/unpublish functionality to DataObjects.

###Install
	// in _config.php
	Object::add_extension('CleanFile', 'CMSPublishableDecorator');

###Public functions
#####getStatus()

	/**
	 * Returns an indicator light,
	 * usefull feature for DataObjectManager etc.
	 *
	 * @return string
	 */
	public function getStatus()

in getCMSFields() we need to extend fields to add publish checkbox on top

	// in child class
	function updateCMSFields_forPopup(FieldSet &$fields)
	// in base class
	$this->extend('updateCMSFields_forPopup', $fields);

##DataObjectSetDecorator
Provides extra functionality to DataObjectSets.
Allows to get a range on the DataObjectSet and custom sorting
###Install
	// in _config.php
	Object::add_extension('DataObjectSet', 'DataObjectSetDecorator');

###Public functions
#####SortedBy($sort)

	/**
	 * Sorting on this DataObjectSet by $sort,
	 * a compound string "[FIELD] [Direction]"
	 * like: "Title ASC"
	 *
	 * @param string $sort
	 * @return DataObjectSet
	 */
	public function SortedBy($sort)

#####Range($param = "")
	/**
	 * Returns a range from this DataObjectSet.
	 * $param should be formated as compund [START_INDEX]_[LENGTH]
	 *
	 * @param string $param
	 * @return DataObjectSet
	 */
	public function Range($param = "")

##FileDecorator
Provides extra functionality to File.
###Install
	// in _config.php
	Object::add_extension('File', 'FileDecorator');
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
##FolderDecorator
Provides extra functionality to Folder.
###Install
	// in _config.php
	Object::add_extension('Folder', 'FolderDecorator');

###Public functions
#####SortedChildFolders()
	/**
	 * Get the children sorted by name of this folder that are also folders.
	 *
	 * @return DataObjectSet
	 */
	function SortedChildFolders()
#####sortedChildren($sort = "Title ASC")
	/**
	 * All subfolders sorted by $sort.
	 *
	 *
	 * @param $sort
	 * @return DataObjectSet
	 */
	public function sortedChildren($sort = "Title ASC")
##GroupLoginDecorator
Provides extra fields to Group, to make custom redirects after login possible. Works together with [CustomLoginForm](Clean_Forms.md#customloginform).
###Install
	// in _config.php
	Object::useCustomClass('MemberLoginForm', 'CustomLoginForm');
	Object::add_extension('Group', 'GroupLoginDecorator');
###db
	"GoToAdmin" => "Boolean"
###has_one
	"LinkPage" => "SiteTree"

##ImageDecorator
Provides extra functionality to Image classes.
###Install
	// in _config.php
	Object::add_extension('Image', 'ImageDecorator');
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
	 * @param int $color
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
##LanguageDecorator
Provides SiteTree classes with a language menu.
###Install
	// in _config.php
	Object::add_extension('Page', 'LanguageDecorator');
###Public functions
#####LanguageChooser()

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
	public function LanguageChooser()

###Template usage

	<div id="Lang">
		<% control LanguageChooser %>
			<a href="$Link" class="lang $Current">
				<% if Locale = de_DE %>DE<% end_if %>
				<% if Locale = en_US %>EN<% end_if %>
			</a>
		<% end_control %>
	</div>

##ModuloDecorator
Provides with some modulo helper functionality for DataObjects when they are within a DataObjectSet.
###Install
	// in _config.php
	Object::add_extension('DataObjectName', 'ModuloDecorator');
###Public functions
#####GetModulo($modulo)
	/**
	 * Indicator for, if this object is the nth child of a collection.
	 *
	 * @param int $modulo
	 * @return bool
	 */
	public function GetModulo($modulo)
#####GetBeforeModulo($modulo)
	/**
	 * Indicator for, if this object is before the nth child of a collection.
	 *
	 * @param int $modulo
	 * @return bool
	 */
	public function GetBeforeModulo($modulo)
#####LessThan($num)
	/**
	 * Indicator for, if this object is the nth child of a collection.
	 *
	 * @param int $modulo
	 * @return bool
	 */
	public function GetModulo($modulo)
#####GetModulo($modulo)
	public function LessThan($num)

##NotificationDecorator
Provides notification/flash messages to SiteTree classes.
###Install
	// in _config.php
	Object::add_extension('Page', 'NotificationDecorator');
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
#####setSessionMessage($str = '',$mode = 'msgGood')

	/**
	 * Adds a message $str to notifications with a certain $mode.
	 *
	 * @param string $str
	 * @param string $mode
	 */
	public function setSessionMessage($str = '',$mode = 'msgGood')
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
			<% control SessionMessages %>
				<p class="$Mode">$Msg</p>
			<% end_control %>
		</div>
	<% end_if %>

##PaginationDecorator
Provides Pagination to SiteTree classes. It also provides a basic "previous/next" page navigation for all childrens of a page.
###Install
	// in _config.php
	Object::add_extension('Page', 'PaginationDecorator');
###db
	'PaginationLimit' => 'Int',
	'Sorting' => 'Text'
###Public functions
#####PageNavigation($param = 'PublishDate_DESC')

	/**
	 *
	 * @param unknown_type $param
	 * @return string|string
	 */
	public function PageNavigation($param = 'PublishDate_DESC')
#####PrevPage($param = 'PublishDate_DESC')

	/**
	 * Returns previous page in stack sorted by $param.
	 * $param should be a compound of [FIELDNAME]_[SORTORDER].
	 *
	 * @param string $param
	 * @return mixed
	 */
	public function PrevPage($param = 'PublishDate_DESC')
#####NextPage($param = 'PublishDate_DESC')

	/**
	 * Returns next page in stack sorted by $param.
	 * $param should be a compound of [FIELDNAME]_[SORTORDER].
	 *
	 * @param string $param
	 * @return mixed
	 */
	public function NextPage($param = 'PublishDate_DESC')
#####CurrentPageNumber($param = 'PublishDate_DESC')

	/**
	 * Returns current page number in stack sorted by $param.
	 * $param should be a compound of [FIELDNAME]_[SORTORDER].
	 *
	 * @param string $param
	 * @return mixed
	 */
	 public function CurrentPageNumber($param = 'PublishDate_DESC')
#####NumberOfSiblings()

	/**
	 * Returns the count of stack items.
	 *
	 * @return int
	 */
	public function NumberOfSiblings()
#####PaginatedChildren()

	/**
	 * Return the paginated collection.
	 *
	 * @return DataObjectSet
	 */
	public function PaginatedChildren()


###Template usage

#####Page Pagination

	<% include PagePagination %>
Includes/PagePagination.ss

	<% if PaginatedChildren.MoreThanOnePage %>
		<div class="pagePagination">
			<% if PaginatedChildren.PrevLink %>
				<a class="control prevPage" href="$PaginatedChildren.PrevLink">&lt;&lt;</a>
			<% else %>
				<span class="control prevPage disabled">&lt;&lt;</span>
			<% end_if %>
			<% control PaginatedChildren.Pages %>
				<% if CurrentBool %>
					<span>$PageNum</span>
				<% else %>
	 				<a href="$Link" title="Go to page $PageNum">$PageNum</a>
				<% end_if %>
			<% end_control %>
			<% if PaginatedChildren.NextLink %>
				<a class="control nextPage" href="$PaginatedChildren.NextLink">&gt;&gt;</a>
			<% else %>
				<span class="control nextPage disabled">&gt;&gt;</span>
			<% end_if %>
		</div>
	<% end_if %>

after it we are able to create a menu for our children pages that will be displayed acording to the values of the previous pagination:

	<% control PaginatedChildren %><div><a href="$Link">$Title</a></div><% end_control %>

#####Page Navigation
we just need to include the following Template to start controlling the previous and next pages, note that the sort order is defined in the template so we can change it by overwritting.

	<% include PageNavigation %>
Includes/PageNavigation.ss

	<% if PageNavigation(Sort_ASC) %>
		<ul class="pageNavigation">
			<li class="pager">$CurrentPageNumber(Sort_ASC) / $NumberOfSiblings</li>
			<% if PrevPage(Sort_ASC) %>
				<li class="control prevPage">
					<% control PrevPage(Sort_ASC) %>
						<a href="$Link" title="<% _t('ProjectPage.PREVOIUS','Previous') %>: $Title"><% _t('PageNavigation.PREVIOUS_PAGE','Previous page') %></a>
					<% end_control %>
				</li>
			<% end_if %>
			<% if NextPage(Sort_ASC) %>
				<li class="control nextPage">
					<% control NextPage(Sort_ASC) %>
						<a href="$Link" title="<% _t('ProjectPage.NEXT','Next') %>: $Title"><% _t('PageNavigation.NEXT_PAGE','Next page') %></a>
					<% end_control %>
				</li>
			<% end_if %>
		</ul>
	<% end_if %>
##SecondMenuDecorator
Provides SiteTree with an extra menu.
Adds a checkbox to Behaviour tab in CMS.
###Install
	// in _config.php
	Object::add_extension('Page', 'SecondMenuDecorator');
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
		<% control SecondMenu %>
			<li>
				<a href="$Link" title="$Title.XML" class="$LinkingMode">$MenuTitle.XML</a>
			</li>
		<% end_control %>
	</ul>

##SiteConfigAddressDecorator

Generates general contact data which can be filled by the website customer.

###Install
	// in _config.php
	Object::add_extension('SiteConfig', 'SiteConfigAddressDecorator');

###db:

	'Company' => 'Text',
	'Address' => 'Text',
	'Country' => 'Text',
	'PLZ' => 'Text',
	'City' => 'Text',
	'Telephone' => 'Text',
	'Cell' => 'Text',
	'Fax' => 'Text',
	'Email' => 'Text'

###Template usage:

	$SiteConfig.Company

##SiteConfigDecorator
Provides SiteConfig with extra fields and adds Google Analytics to your site.
###Install
	// in _config.php
	Object::add_extension('SiteConfig', 'SiteConfigDecorator');

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

	$GoogleAnalytics


##TextDecorator
Provides a couple of helper methods to Text classes.
###Install
	// in _config.php
	Object::add_extension('HTMLText', 'TextDecorator');

###db:

	'Copyright' => 'Text'
	'GoogleAnalyticsTrackingCode' => 'Text'

###Public static functions
#####html_obfuscate($text)
	/**
	 * Obfuscates a given string into html character entities.
	 *
	 * @param string $text
	 * @return string
	 */
	public static function html_obfuscate($text)
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
#####ConvertText($limit = 20)
	/**
	 * Converts a given text into uft8 and shortens it to $limit.
	 *
	 * @param int $limit
	 * @param string $add
	 * @return string
	 */
	public function ConvertText($limit = 20)
#####LimitCharactersUTF($limit = 20, $add = "...")
	/**
	 * Converts a given text into uft8 and
	 * shortens it by $limit and adds $add.
	 *
	 * @param int $limit
	 * @param string $add
	 * @return string
	 */
	public function LimitCharactersUTF($limit = 20, $add = "...")
#####EmailObfuscated()
	/**
	 * Returns a representation of this text
	 * with all email addresses converted into html character entities.
	 *
	 * @return string
	 */
	public function EmailObfuscated()
#####CheckWordCount($numWords = 26)
	/**
	 * Tests if the text is longer than $numWords.
	 *
	 * @param int $numWords
	 * @return ArrayData
	 */
	function CheckWordCount($numWords = 26)
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


##ThemeDecorator

Provides custom template choosing functionality, which lets you set a Template to a page. Works together with [ThemeExtension](Clean_Extensions.md#themeextension).

###Install
	// in _config.php
	Object::add_extension('Page', 'ThemeDecorator');
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


##TranslatableDataObjectDecorator
A simple decorator class that adds translatable fields to a given DataObject subclass. Unlike the {@link Translatable} module, this class does not require a CMS interface and therefore can be applied beyond SiteTree descendants. Good use case are small DataObjects in has_many/ many_many relations, when it comes to translations.

This is a Silverstripe 2.4 port of [Uncle Cheese´s TranslatableDataObject](https://github.com/unclecheese/TranslatableDataObject).

Use with caution

##UtilitiesDecorator
Provides a couple of helper methods to the SiteTree instances.

###Install
	// in _config.php
	'Object::add_extension('Page', 'UtilitiesDecorator');
###db

	'PublishDate' => 'Datetime'

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
	<% control MemberGroup(1) %>
		<!-- Will print all ADMIN members here… -->
		$Email $FullName
	<% end_control %>

	<% control PageInstance(SomePageClass) %>
		<!-- All public page stuff available here… -->
		$Title, etc...
	<% end_control %>

	<% control PageControllerInstance(SomePageClass) %>
		<!-- All public controller stuff available here... -->
		$Form
	<% end_control %>

	$ShortLang <!-- DE -->

