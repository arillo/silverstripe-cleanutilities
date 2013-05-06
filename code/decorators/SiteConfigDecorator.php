<?php
/**
 * Provides SiteConfig with extra fields and adds Google Analytics to your site.
 *
 * Add this extension to a SiteConfig instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('SiteConfig', 'SiteConfigDecorator');
 *
 * @package cleanutilities
 * @subpackage decorators
 *
 * @author arillo
 */
class SiteConfigDecorator extends DataObjectDecorator{

	/**
	 * Adds fields to your site config.
	 * Fields:
	 *   'Copyright' => 'Text',
	 *   'GoogleAnalyticsTrackingCode' => 'Text'
	 *
	 * @return array
	 */
	function extraStatics() {
		return array(
			'db' => array(
				'Copyright' => 'Text',
				'GoogleAnalyticsTrackingCode' => 'Text'
			)
		);
	}

	/**
	 * Adds the new fields to CMS form.
	 *
	 * @param $fields
	 */
	public function updateCMSFields(FieldSet &$fields) {
		$fields->addFieldToTab("Root.Main", new TextField("Copyright", _t('SiteConfig.COPYRIGHT', "Copyright")));
		$fields->addFieldToTab("Root.Main", new TextField("GoogleAnalyticsTrackingCode", _t('SiteConfig.ANALYTICS', "GoogleAnalyticsTrackingCode")));
	}

	/**
	 * Adds Google Analytics javascript to your template
	 */
	public function GoogleAnalytics(){
		if(Director::isLive() && $this->owner->GoogleAnalyticsTrackingCode != ''){
			Requirements::customScript("
				var _gaq = _gaq || [];
				_gaq.push(['_setAccount', '".$this->owner->GoogleAnalyticsTrackingCode."']);
				_gaq.push(['_trackPageview']);

				(function() {
					var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
					ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
					var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
				})();
			");
		}
	}
}