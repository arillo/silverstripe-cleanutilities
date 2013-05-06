<?php
/**
 * Provides Pagination to SiteTree classes.
 *
 * Add this extension to a SiteConfig instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('Page', 'PaginationDecorator');
 *
 * @package cleanutilities
 * @subpackage decorators
 *
 * @author arillo
 */
class PaginationDecorator extends DataObjectDecorator{

	/**
	 * Adds fields to your site config.
	 *
	 * Fields:
	 *  'PaginationLimit' => 'Int',
	 *	'Sorting' => 'Text'
	 *
	 * @return array
	 */
	function extraStatics() {
		return array(
			'db' => array(
				'PaginationLimit' => 'Int',
				'Sorting' => 'Text'
			)
		);
	}

	/**
	 * Adds the new fields to CMS form.
	 *
	 * @param $fields
	 */
	public function updateCMSFields(FieldSet &$fields) {
		$fields->addFieldToTab("Root.Content.Pagination",new NumericField('PaginationLimit','PaginationLimit'));
		$fields->addFieldToTab("Root.Content.Pagination",new TextField('Sorting','Sorting Criteria'));
	}

	/**
	 *
	 * @param unknown_type $param
	 * @return string|string
	 */
	public function PageNavigation($param = 'PublishDate_DESC'){
		if($this->owner->PrevPage($param) || $this->owner->NextPage($param)){
			return true;
		}
		return false;
	}

	/**
	 * Returns previous page in stack sorted by $param.
	 * $param should be a compound of [FIELDNAME]_[SORTORDER].
	 *
	 * @param string $param
	 * @return mixed
	 */
	public function PrevPage($param = 'PublishDate_DESC'){
		$scriteria = explode("_", $param);
		$sorting = $scriteria[0].' '.$scriteria[1];
		if($this->owner->ParentID && count($scriteria) == 2){
			$pagenumber = ($this->owner->CurrentPageNumber($param))-2;
			if($pagenumber == -1) return false;

			return DataObject::get("SiteTree", "ParentID = {$this->owner->ParentID}", $sorting, null, "{$pagenumber},1");
		}
		return false;
	}

	/**
	 * Returns next page in stack sorted by $param.
	 * $param should be a compound of [FIELDNAME]_[SORTORDER].
	 *
	 * @param string $param
	 * @return mixed
	 */
	public function NextPage($param = 'PublishDate_DESC'){
		$scriteria = explode("_", $param);
		$sorting = $scriteria[0].' '.$scriteria[1];
	if($this->owner->ParentID && count($scriteria) == 2){
			$pagenumber = ($this->owner->CurrentPageNumber($param));
			return DataObject::get("SiteTree", "ParentID = {$this->owner->ParentID}", $sorting, null, "{$pagenumber},1");
		} else{
			return false;
		}
	}

	/**
	 * Returns current page number in stack sorted by $param.
	 * $param should be a compound of [FIELDNAME]_[SORTORDER].
	 *
	 * @param string $param
	 * @return mixed
	 */
	public function CurrentPageNumber($param = 'PublishDate_DESC'){
		$scriteria = explode("_", $param);
		$sorting = $scriteria[0].' '.$scriteria[1];
		if($this->owner->ParentID && count($scriteria) == 2){
			$count = 0;
			$sorting = $scriteria[0].' '.$scriteria[1];
			$articles = DataObject::get("SiteTree", "ParentID = ".$this->owner->ParentID, $sorting);
			foreach($articles as $article){
				if($scriteria[1] == 'ASC'){
					if($article->$scriteria[0] > $this->owner->$scriteria[0]){
						return $count;
					}
				}
				if($scriteria[1] == 'DESC'){
					if($article->$scriteria[0] < $this->owner->$scriteria[0]){
						return $count;
					}
				}
				$count++;
			}
			return $count;
		}
	}

	/**
	* Returns the count of stack items.
	*
	* @return int
	*/
	public function NumberOfSiblings(){
		if($this->owner->ParentID){
			return (DataObject::get("SiteTree","ParentID = ".$this->owner->ParentID)->Count());
		}
	}

	/**
	 * Return the paginated collection.
	 *
	 * @return DataObjectSet
	 */
	public function PaginatedChildren(){

		if($this->owner->Sorting == "") $sort = 'Sort ASC';
		else $sort = $this->owner->Sorting;
		if(!isset($_GET['start']) || !is_numeric($_GET['start']) || (int)$_GET['start'] < 1) $_GET['start'] = 0;
		$offset = (int)$_GET['start'];
		$limit = $this->owner->PaginationLimit;
		$range = $offset.",".$limit;
		if(!$limit) $range = 0;
		return DataObject::get('Page',"ParentID = ".$this->owner->ID,$sort,"",$range);
	}
}