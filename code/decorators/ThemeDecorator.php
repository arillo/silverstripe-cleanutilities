<?php
/**
 * Provides custom template choosing functionality
 * which lets us set a Template to a page.
 * Works together with ThemeExtension
 *
 * Add this extension to a SiteTree instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('Page', 'ThemeDecorator');
 * Object::add_extension('Page_Controller', 'ThemeExtension');
 *
 *
 * @package cleanutilities
 * @subpackage decorators
 *
 * @author arillo
 */
class ThemeDecorator extends DataObjectDecorator{

	/**
	 * Adds field to save the template name
	 *
	 * @return array
	 */
	function extraStatics() {
		return array(
			'db' => array(
				'Template'	=> 'Varchar'
			)
		);
	}

	/**
	 * Adds template selection to CMS form and descriptive info about
	 * the currently used template.
	 *
	 * @param $fields
	 */
	public function updateCMSFields(FieldSet &$fields){
		$TemplateField = new DropdownField(
			"Template",
			"Template",
			$this->getSelectableTemplates(),
			$this->owner->Template
		);
		$fields->addFieldToTab("Root.Content.Template",$TemplateField);
		if($this->owner->Template){
			$fields->addFieldToTab(
				'Root.Content.Main',
				new LiteralField('TemplateDescription','<h4 style="color:#000;font-size: 16px; margin-bottom: 10px; padding-top: 0;"><span style="font-weight: normal; font-size: 14px">Template</span>: '.$this->owner->Template.'</h4>'), 'Title');
		}
	}

	/**
	 * Returns a relative path to current theme directory.
	 *
	 * @return mixed
	 */
	function ThemeDir(){
		if($theme = SSViewer::current_theme()) return THEMES_DIR . "/$theme";
		else if($theme = SiteConfig::current_site_config()->Theme ) return THEMES_DIR . "/$theme";

		 return false;
	}

	/**
	 * Returns a relative path to current template file.
	 *
	 * @return string
	 */
	function TemplateFile(){
		return $this->TemplateDir().$this->owner->Template.".ss";
	}

	/**
	 * Returns a absolute path to current template file.
	 *
	 * @return string
	 */
	function TemplateAbsFile(){
		return Director::getAbsFile($this->TemplateFile());
	}

	/**
	 * Returns the current template directory.
	 *
	 * @param string $directory
	 * @return string
	 */
	function TemplateDir($directory = 'Layout/'){
		return $this->ThemeDir()."/templates/".$directory;
	}

	/**
	 * Returns an array of all selectable template files.
	 *
	 * @param string $directory
	 * @return array
	 */
	public function getSelectableTemplates($directory = 'Layout/'){
		$temp = array("" => "None");
		$pre = "";
		if($TemplateFiles = glob(Director::getAbsFile($this->TemplateDir($directory)).$pre."*.ss")) {
			foreach($TemplateFiles as $TemplateFile) {
				$filename = basename($TemplateFile, ".ss");
				if($filename != $pre) $filenicename = $filename;
				else $filenicename = "Default";

				$filenicename = str_replace("col", " Column", $filenicename);
				$temp[$filename] = ucwords($filenicename);
			}
		}
		return $temp;
	}
}