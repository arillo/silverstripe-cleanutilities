<?php
/**
 * Provides publish/unpublish functionality to DataObjects.
 *
 * Add this extension to a DataObject instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('CleanFile', 'CMSPublishableDecorator');
 *
 * @package cleanutilities
 * @subpackage decorators
 *
 * @author arillo
 */
class CMSPublishableDecorator extends DataObjectDecorator{
	function extraStatics() {
		return array(
			'db' => array(
				'Published' => 'Boolean'
			)
		);
	}
	/**
	 * Returns an indicator light,
	 * usefull feature for DataObjectManager etc.
	 *
	 * @return string
	 */
	public function getStatus(){
		if($this->owner->Published == true){
			return '<div title="published" class="published" style="background:#33CC00;-webkit-border-radius: 50%;-moz-border-radius: 50%;border-radius: 50%;height: 11px;overflow: hidden;text-indent: -99999px;width: 11px;direction:ltr;">published</div>';
		}else{
			return '<div title="unpublished" class="unpublished" style="background:#DE461F;-webkit-border-radius: 50%;-moz-border-radius: 50%;border-radius: 50%;height: 11px;overflow: hidden;text-indent: -99999px;width: 11px;direction:ltr;">unpublished</div>';
		}
	}
	function getCleanFields(FieldSet &$fields){
		$fields->insertBefore(new CheckboxField('Published','Published'),'Title');
		return $fields;
	}
	function updateCMSFields_forPopup(FieldSet &$fields){
		$fields->insertBefore(new CheckboxField('Published','Published'),'Title');
		return $fields;
	}
}