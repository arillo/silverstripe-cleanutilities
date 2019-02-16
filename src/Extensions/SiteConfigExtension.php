<?php
namespace Arillo\CleanUtilities\Extensions;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Control\Director;
use SilverStripe\View\Requirements;
use SilverStripe\ORM\DataExtension;

/**
 * Provides SiteConfig with extra fields and adds Google Analytics to your site.
 *
 * Add this extension to a SiteConfig instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('SiteConfig', 'SiteConfigExtension');
 *
 * @package cleanutilities
 * @subpackage data_extensions
 *
 * @author arillo
 */
class SiteConfigExtension extends DataExtension
{
    private static $db = array(
        'Copyright' => 'Text',
        'GoogleAnalyticsTrackingCode' => 'Text',
    );

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldToTab("Root.Main", TextField::create("Copyright", _t('SiteConfig.COPYRIGHT', "Copyright")));
        $fields->addFieldToTab("Root.Main", TextField::create("GoogleAnalyticsTrackingCode", _t('SiteConfig.ANALYTICS', "GoogleAnalyticsTrackingCode")));
    }

    public function GoogleAnalytics()
    {
        if (Director::isLive() && $this->owner->GoogleAnalyticsTrackingCode != '') {
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
