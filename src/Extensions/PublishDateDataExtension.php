<?php
namespace Arillo\CleanUtilities\Extensions;

use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\{
    DatetimeField,
    FieldList
};


/**
 * Provides PublishDate for Pages
 *
 * Add this extension to a SiteTree instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('Page', 'PublishDateDataExtension');
 *
 * @package cleanutilities
 * @subpackage data_extensions
 *
 * @author arillo
 */
class PublishDateDataExtension extends DataExtension
{

    private static $db = array(
        'PublishDate' => 'Datetime'
    );

    /**
     * Automatically sets PublishDate to now, if is empty.
     */
    public function onBeforeWrite()
    {
        if (
            $this->owner->ID
            && $this->owner->PublishDate == ''
        ) {
            $this->owner->PublishDate = date('Y-m-d H:i:s', strtotime('now'));
        }
        parent::onBeforeWrite();
    }

    public static function datetime_field()
    {
        return DatetimeField::create(
            'PublishDate',
            _t('CMSPublishableDataExtension.PUBLISH_DATE', 'Publish date')
        );
    }
}
