<?php
/**
 * Provides a couple of helper methods to 
 * the SiteTree instances.
 * 
 * Add this extension to a SiteTree instance
 * by adding this to your _config.php:
 * 
 * Object::add_extension('Page', 'UtilitiesDataExtension');
 * 
 * @package cleanutilities
 * @subpackage data_extensions
 * 
 * @author arillo
 */
class UtilitiesDataExtension extends DataExtension
{

    /**
     * Add page class info field to cms
     * @param  FieldList $fields [description]
     */
    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldToTab('Root.Main', new LiteralField('ClassNameDescription', '<h4 style="color:#000;font-size: 16px; margin-bottom: 10px; padding-top: 10px;"><span style="font-weight: normal; font-size: 14px">Locale</span>: '.$this->owner->Locale.' <span style="font-weight: normal; font-size: 14px">ID</span>: '.$this->owner->ID.' <span style="font-weight: normal; font-size: 14px">Type:</span> '.$this->owner->i18n_singular_name().'</h4>'), 'Title');
    }

    /**
     * Returns all members of a group by ID .
     * 
     * @param int $ID  group ID
     * @return DataList
     */
    public function MemberGroup($ID = 1)
    {
        if ($group = DataObject::get('Group')->byID($ID)) {
            return $group->Members();
        }
        return false;
    }

    /**
     * Returns a SiteTree instance by ClassName.
     * 
     * @param string $pagetype
     * @return mixed DataObject|bool
     */
    public function PageInstance($pagetype = 'Page')
    {
        if (is_subclass_of($pagetype, 'SiteTree')) {
            if ($page = $pagetype::get()->First()) {
                return $page;
            }
        }
        return false;
    }
    
    /**
     * Return a Page_Controller instance by page ClassName.
     * 
     * @return mixed Page_Controller|bool
     */
    public function PageControllerInstance($pagetype = 'Page')
    {
        if ($page = $this->PageInstance($pagetype)) {
            $controllerClass = $pagetype . "_Controller";
            return new $controllerClass($page);
        }
        return false;
    }
    
    /**
     * Returns shortlang from Locale.
     * 
     * @return string
     */
    public function ShortLang()
    {
        return i18n::get_lang_from_locale($this->owner->Locale);
    }
}
