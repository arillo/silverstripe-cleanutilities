<?php
namespace Arillo\CleanUtilities\CMS;

use SilverStripe\Forms\GridField\{
    GridField,
    GridFieldConfig_RelationEditor,
    GridFieldAddExistingAutocompleter
};

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\Assets\File;
use SilverStripe\Core\Config\Config;
use SilverStripe\AssetAdmin\Forms\UploadField;
// use SilverStripe\SelectUpload\SelectUploadField;

use Arillo\CleanUtilities\Extensions\SortableDataExtension;

class Fields
{
    const DEFAULT_TAB = 'Root.Main';
    const IMAGE_EXTENSIONS = [
        '', 'bmp','png','gif','jpg','jpeg','ico','pcx','tif','tiff'
    ];
    const DEFAULT_UPLOAD_FOLDER = 'Uploads';

    public static function sortable_manager(
        string $relationName,
        string $label,
        $data,
        string $sortField = SortableDataExtension::SORT_FIELD
    ) {
        return SortableDataExtension::make_gridfield_sortable(
            GridField::create(
                $relationName,
                $label,
                $data,
                GridFieldConfig_RelationEditor::create()
                    ->removeComponentsByType(GridFieldAddExistingAutocompleter::class)
            ),
            $sortField
        );
    }

    /**
     * @param  FieldList $fields
     * @return FieldList
     */
    public static function remove_cleanfields(FieldList $fields)
    {
        return $fields
            ->removeByName('Images')
            ->removeByName('Links')
            ->removeByName('Videos')
            ->removeByName('Header')
            ->removeByName('Files')
        ;
    }

    /**
     * Default config for image upload field.
     *
     * @return array
     */
    public static function default_image_config()
    {
        return [
            'tab' => self::DEFAULT_TAB, // tab name to inject
            'field' => 'Attachment', // field name
            'label' => _t(__CLASS__ . '.Attachment', 'Image'), // field label
            'description' => null, // field description
            'insertBefore' => null, // insert before some other field
            'folderName' => 'images',
            'allowedExtensions' => self::IMAGE_EXTENSIONS,
        ];
    }

    /**
     * Default config for file upload field.
     *
     * @return array
     */
    public static function default_file_config()
    {
        return [
            'tab' => self::DEFAULT_TAB, // tab name to inject
            'field' => 'Attachment', // field name
            'label' => _t(__CLASS__ . '.FileLabel', 'File'), // field label
            'description' => null, // field description
            'insertBefore' => null, // insert before some other field
            'folderName' => 'files',
            'allowedExtensions' => Config::inst()->get(File::class, 'allowed_extensions'),
        ];
    }

    public static function fields_config(
        array $defaults = [],
        array $config = []
    ) {
        return array_merge($defaults, $config);
    }

    /**
     * Add image upload field to field list.
     *
     * @param  DataObject $record
     * @param  FieldList  $fields
     * @param  array      $config
     * @return FieldList
     */
    public static function add_image_field(
        DataObject $record,
        FieldList $fields,
        array $config = []
    ) {
        return self::add_upload_field(
            $record,
            $fields,
            self::fields_config(self::default_image_config(), $config)
        );
    }

    /**
     * Add file upload field to field list.
     *
     * @param DataObject $record
     * @param FieldList  $fields
     * @param array      $config
     * @return FieldList
     */
    public static function add_file_field(
        DataObject $record,
        FieldList $fields,
        array $config = []
    ) {
        return self::add_upload_field(
            $record,
            $fields,
            self::fields_config(self::default_file_config(), $config)
        );
    }

    /**
     * Generic add upload field to field list.
     *
     * @param DataObject $record
     * @param FieldList  $fields
     * @param array      $config
     */
    public static function add_upload_field(
        DataObject $record,
        FieldList $fields,
        array $config
    ) {
        $addFields = [
            // SelectUploadField::create($config['field'], $config['label'])
            UploadField::create($config['field'], $config['label'])
                ->setDescription($config['description'] ?? implode($config['allowedExtensions'], ', '))
                ->setAllowedMaxFileNumber(1)
                ->setAllowedExtensions($config['allowedExtensions'])
                ->setFolderName($config['folderName'])
        ];

        return $fields->addFieldsToTab(
            $config['tab'],
            $addFields,
            $config['insertBefore']
        );
    }
}
