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
    /**
     * Adds PublishDate to CMS Form.
     * @param $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        $datefield = DatetimeField::create(
            'PublishDate',
            _t('CMSPublishableDataExtension.PUBLISH_DATE', 'Publish date')
        );
        // $datefield->getDateField()->setConfig('showcalendar', 1);
        // $datefield->setConfig('setLocale', en_US);
        $fields->addFieldToTab("Root.Main", $datefield, 'Content');
    }
}
