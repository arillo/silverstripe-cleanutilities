<?php
/**
 * Provides a couple of helper methods for 
 * Theme handling and lets us set a Template 
 * other than the default on to this page controller.
 * 
 * Add this extension to a controller
 * by adding this to your _config.php:
 * 
 * Object::add_extension('Page_Controller', 'ThemeExtension');
 * 
 * @package cleanutilities
 * @subpackage data_extensions
 * 
 * @author arillo
 */
class ThemeExtension extends Extension
{

    /**
     * Renders the decorated page with a given template.
     * @return array
     */
    public function index()
    {
        if ($this->owner->Template != '') {
            return $this->owner->renderWith(
                array(
                    $this->owner->Template,
                    'Page'
                )
            );
        }
        return array();
    }
}
