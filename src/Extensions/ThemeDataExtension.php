<?php
namespace Arillo\CleanUtilities\Extensions;

use SilverStripe\Forms\{
    FieldList,
    DropdownField,
    LiteralField
};

use SilverStripe\View\SSViewer;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\Control\Director;
use SilverStripe\ORM\DataExtension;
use Exception;

/**
 * Provides custom template choosing functionality
 * which lets us set a Template to a page.
 * Works together with ThemeExtension
 *
 * Add this extension to a SiteTree instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('Page', 'ThemeDataExtension');
 * Object::add_extension('Page_Controller', 'ThemeExtension');
 *
 *
 * @package cleanutilities
 * @subpackage utils_extensions
 *
 * @author arillo
 */
class ThemeDataExtension extends DataExtension
{
    private static $db = array(
        'Template' => 'Varchar(255)'
    );

    public function updateCMSFields(FieldList $fields)
    {
        $templateField = DropdownField::create(
            "Template",
            "Template",
            $this->getSelectableTemplates(),
            $this->owner->Template
        );
        $fields->addFieldToTab("Root.Main", $templateField, 'Content');
        if ($this->owner->Template) {
            $fields->addFieldToTab('Root.Main', LiteralField::create('TemplateDescription', '<h4 style="color:#000;font-size: 16px; margin-bottom: 10px; padding-top: 0;"><span style="font-weight: normal; font-size: 14px">Template</span>: '.$this->owner->Template.'</h4>'), 'Title');
        }
    }

    /**
     * Returns a relative path to current theme directory.
     *
     * @return mixed
     */
    public function ThemeDir()
    {
        if ($theme = SSViewer::current_theme()) {
            return THEMES_DIR . "/$theme";
        } elseif ($theme = SSViewer::current_custom_theme()) {
            return THEMES_DIR . "/$theme";
        } elseif ($theme = SiteConfig::current_site_config()->Theme) {
            return THEMES_DIR . "/$theme";
        } else {
            throw new Exception("cannot detect theme");
        }
    }

    /**
     * Returns a relative path to current template file.
     *
     * @return string
     */
    public function TemplateFile()
    {
        return $this->TemplateDir().$this->owner->Template.".ss";
    }

    /**
     * Returns a absolute path to current template file.
     *
     * @return string
     */
    public function TemplateAbsFile()
    {
        return Director::getAbsFile($this->TemplateFile());
    }

    /**
     * Returns the current template directory.
     *
     * @param string $directory
     * @return string
     */
    public function TemplateDir($directory = 'Layout/')
    {
        return $this->ThemeDir()."/templates/".$directory;
    }

    /**
     * Returns an array of all selectable template files.
     *
     * @param string $directory
     * @return array
     */
    public function getSelectableTemplates($directory = 'Layout/')
    {
        $temp = array("" => "None");
        $pre = "";
        if ($TemplateFiles = glob(Director::getAbsFile($this->TemplateDir($directory)).$pre."*.ss")) {
            foreach ($TemplateFiles as $TemplateFile) {
                $filename = basename($TemplateFile, ".ss");
                if ($filename != $pre) {
                    $filenicename = $filename;
                } else {
                    $filenicename = "Default";
                }

                $filenicename = str_replace("col", " Column", $filenicename);
                $temp[$filename] = ucwords($filenicename);
            }
        }
        return $temp;
    }
}
