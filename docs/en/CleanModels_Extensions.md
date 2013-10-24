#Clean Models DataExtensions
__written by Arillo__

All of this DataExtensions can provide SiteTree classes with has_many of its corresponding [Clean Model](Clean_Models.md) features. These has-many relationships are managed through a GridField in the CMS. All DataExtensions will create an own tab in the CMS, where their collection can be edited. They all can be installed like this:
####Install
	// add Models Decorators in _config.php
	Object::add_extension('Page', 'CleanImagesExtension');
	Object::add_extension('Page', 'FilesExt…
	..
	.


####Template usage
All of this DataExtensions come with similar function patterns. Just inspect the "public function" sections to learn more. For example:

	<% loop Images(0, 1, Title, DESC) %>
		$Title
		$Attachment
	<% end_loop %>

	or

	<% if MoreFilesThan(3) %>
		We have more than 3 Files attached to this page.
	<% end_if %>

	etc...

##CleanFilesExtension
Implements many manageable files ([CleanFile](Clean_Models.md#cleanfile)) to a SiteTree class.

####has_many

	'CleanFiles' => 'CleanFile'

###Public functions
####Files($limit = 0, $offset = 0, $sortField = 'SortOrder', $sortDir = 'ASC')

	/**
	 * Getter for the attached files.
	 * You can specifiy a range and sorting of those files.
	 * 
	 * @param int $limit
	 * @param int $offset
	 * @param string $sortField
	 * @param string $sortDir
	 * @return DataList
	 */
	public function Files($limit = 0, $offset = 0, $sortField = 'SortOrder', $sortDir = 'ASC')
####FileAttachment($index = 0, $sortField = 'SortOrder', $sortDir = 'ASC')
	/**
	 * Getter for a specific file's attachment by $index.
	 * 
	 * @param int $index
	 * @return File|boolean
	 */
	public function FileAttachment($index = 0, $sortField = 'SortOrder', $sortDir = 'ASC')
####FilesAttachment($limit = 0, $offset = 0, $sortField = 'SortOrder', $sortDir = 'ASC')
	/**
	 * Getter for a sortable range of file's attachments.
	 * 
	 * @param int $limit
	 * @param int $offset
	 * @param string $sortField
	 * @param string $sortDir
	 * @return ArrayList|boolean
	 */
	public function FilesAttachment($limit = 0, $offset = 0, $sortField = 'SortOrder', $sortDir = 'ASC')
####MoreFilesThan($num = 0)
	/**
	 * Tests if the count of files is higher than $num.
	 * 
	 * @param int $num
	 * @return boolean
	 */
	public function MoreFilesThan($num = 0)



##CleanImagesExtension
Implements many manageable images ([CleanImage](Clean_Models.md#cleanimage)) to a SiteTree class.

####has_many

	'CleanImages' => 'CleanImage'

###Public functions
####Images($limit = 0, $offset = 0, $sortField = 'SortOrder', $sortDir = 'ASC')
	/**
	 * Getter for the attached images.
	 * You can specifiy a range and sorting of those images.
	 * 
	 * @param int $limit
	 * @param int $offset
	 * @param string $sortField
	 * @param string $sortDir
	 * @return DataList
	 */
	public function Images($limit = 0, $offset = 0, $sortField = 'SortOrder', $sortDir = 'ASC')
####ImageAttachment($index = 0, $sortField = 'SortOrder', $sortDir = 'ASC')
	/**
	 * Getter for a specific image's attachment by $index.
	 * 
	 * @param int $index
	 * @param string $sortField
	 * @param string $sortDir
	 * @return Image|boolean
	 */
####ImagesAttachment($limit = 0, $offset = 0, $sortField = 'SortOrder', $sortDir = 'ASC')
	/**
	 * Getter for a sortable range of images's attachments.
	 * 
	 * @param int $limit
	 * @param int $offset
	 * @param string $sortField
	 * @param string $sortDir
	 * @return DataList|boolean
	 */
	public function ImagesAttachment($limit = 0, $offset = 0, $sortField = 'SortOrder', $sortDir = 'ASC')
####MoreImagesThan($num = 0)
	/**
	 * Tests if the count of images is higher than $num.
	 * 
	 * @param int $num
	 * @return boolean
	 */
	public function MoreImagesThan($num = 0)
##CleanLinksDecorator
Implements many manageable links ([CleanLink](Clean_Models.md#cleanlink)) to a SiteTree class.
####has_many

	'CleanLinks' => 'CleanLink'

###Public functions
####Links($limit = 0, $offset = 0, $sortField = 'SortOrder', $sortDir = 'ASC')

	/**
	 * Getter for the attached links.
	 * You can specifiy a range of those links.
	 * 
	 * @param int $limit
	 * @param int $offset
	 * @param string $sortField
	 * @param string $sortDir
	 * @return DataList
	 */
	public function Links($limit = 0, $offset = 0, $sortField = 'SortOrder', $sortDir = 'ASC')
####MoreLinksThan($num = 0)
	/**
	 * Tests if the count of videos is higher than $num.
	 * 
	 * @param int $num
	 * @return bool
	 */
	public function MorelinksThan($num = 0)

##CleanTeasersExtension
Implements many manageable teasers to a SiteTree class. Read more about ([CleanTeaser](Clean_Models.md#cleanteaser)).

####has_many

	CleanTeasers' => 'CleanTeaser'

###Public functions
####Teasers($limit = 0, $offset = 0, $sortField = 'SortOrder', $sortDir = 'ASC')
	/**
	 * Getter for the attached teasers.
	 * You can specifiy a range of those links.
	 * 
	 * @param int $limit
	 * @param int $offset
	 * @param string $sortField
	 * @param string $sortDir
	 */
	public function Teasers($limit = 0, $offset = 0, $sortField = 'SortOrder', $sortDir = 'ASC')
####MoreTeasersThan($num = 0)
	/**
	 * Tests if the count of teasers is higher than $num.
	 * 
	 * @param int $num
	 * @return bool
	 */
	public function MoreTeasersThan($num = 0)

##CleanVideosExtension
Implements many manageable videos to a SiteTree class. Read more about ([CleanTeaser](Clean_Models.md#cleanteaser)).

####has_many

	'CleanVideos' => 'CleanVideo'

###Public functions
####Videos($limit = 0, $offset = 0, $sortField = 'SortOrder', $sortDir = 'ASC')
	/**
	 * Getter for the attached teasers.
	 * You can specifiy a range of those videos.
	 * 
	 * @param int $limit
	 * @param int $offset
	 * @param string $sortField
	 * @param string $sortDir
	 * @return DataList
	 */
	public function Videos($limit = 0, $offset = 0, $sortField = 'SortOrder', $sortDir = 'ASC')
####MoreVideosThan($num = 0)
	/**
	 * Tests if the count of videos is higher than $num.
	 * 
	 * @param int $num
	 * @return bool
	 */
	public function MoreVideosThan($num = 0)




