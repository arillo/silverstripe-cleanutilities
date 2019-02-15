<?php
/**
 * Provides extra fields to Group,
 * to make custom redirects after login possible.
 * Works together with CustomRedirectLoginForm
 * 
 *
 * Add this extension to a Group instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('Group', 'GroupLoginDataExtension');
 *
 * // CustomRedirectLoginForm
 * Object::useCustomClass('MemberLoginForm', 'CustomRedirectLoginForm');
 *
 * @package cleanutilities
 * @subpackage data_extensions
 *
 * @author arillo
 */
class GroupLoginDataExtension extends DataExtension
{
    
    
    private static $db = array(
        "GoToAdmin" => "Boolean"
    );
    
    private static $has_one = array(
        "LinkPage" => "SiteTree"
    );
    
    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldToTab(
            "Root.Members",
            CheckboxField::create(
                "GoToAdmin",
                _t('GroupLoginDataExtension.GO_ADMIN', 'Go to Admin area')
            ),
            'Members'
        );
        $fields->addFieldToTab(
            "Root.Members",
            TreeDropdownField::create(
                "LinkPageID",
                _t('GroupLoginDataExtension.REDIRECT_PAGE', 'Or select a Page to redirect to'),
                "SiteTree"
            ),
            'Members'
        );
    }
}
