#Models Decorators
__written by Arillo__

Each Model Decorator can provide SiteTree classes with has_many of its corresponding [Clean Model](Clean_Models) features. These has-many relationships are managed through DataObjectManager in the CMS. All Models Decorators will create an own tab in the CMS, where their collection can be edited. They all can be installed like this:
####Install
	// add Models Decorators in _config.php
	Object::add_extension('Page', 'ImagesDecorator');
	Object::add_extension('Page', 'FilesDeco…
	..
	.


####Template usage
All Models Decorators come with similar function patterns. Just inspect the "public function" sections to learn more. For example:

	<% control Images(0,1) %>
		$Title
		$Attachment
	<% end_control %>

	or

	<% if MoreFilesThan(3) %>
		We have more than 3 Files attached to this page.
	<% end_if %>

	etc...


##ImagesDecorator
Implements many manageable images ([CleanImage](Clean_Models#cleanimage)) to a SiteTree class.

####has_many

	'CleanImages' => 'CleanImage'

###Public functions

	public function Images($offset = 0, $limit = 0)	public function ImageAttachmnet($index = 0)
	public function ImagesAttachment($offset = 0, $limit = 0)
	public function MoreImagesThan($num = 0)

##FilesDecorator
Implements many manageable files ([CleanFile](Clean_Models#cleanfile)) to a SiteTree class.

####has_many

	'CleanFiles' => 'CleanFile'

###Public functions

	public function Files($offset = 0, $limit = 0)
	public function FileAttachmnet($index = 0)
	public function FilesAttachment($offset = 0, $limit = 0)
	public function MoreFilesThan($num = 0)

##TeasersDecorator
Implements many manageable teasers to a SiteTree class. Read more about ([CleanTeaser](Clean_Models#cleanteaser)).

####has_many

	CleanTeasers' => 'CleanTeaser'

###Public functions

	public function Teasers($offset = 0,$limit = 0)
	public function MoreTeasersThan($num = 0)

##LinksDecorator
Implements many manageable links ([CleanLink](Clean_Models#cleanlink)) to a SiteTree class.
####has_many

	'CleanLinks' => 'CleanLink'

###Public functions

	public function Links($offset = 0,$limit = 0)
	public function MoreLinksThan($num = 0)

