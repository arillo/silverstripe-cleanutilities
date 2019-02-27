<?php
namespace Arillo\CleanUtilities\Migrations;

use SilverStripe\Dev\BuildTask;
use SilverStripe\Control\Director;
use SilverStripe\ORM\Queries\SQLUpdate;
use SilverStripe\AssetAdmin\Helper\ImageThumbnailHelper;
use SilverStripe\AssetAdmin\Controller\AssetAdmin;
use SilverStripe\Assets\File;

use Arillo\CleanUtilities\Models\{
    CleanFile,
    CleanImage,
    CleanLink,
    CleanTeaser,
    CleanVideo
};

class CleanMigration extends BuildTask
{
    const TYPES = [
        'models',
        'thumbs',
    ];

    const MODEL_CLASSES = [
        'CleanFile' => CleanFile::class,
        'CleanImage' => CleanImage::class,
        'CleanLink' => CleanLink::class,
        'CleanTeaser' => CleanTeaser::class,
        'CleanVideo' => CleanVideo::class,
    ];

    public static function echo(string $message)
    {
        switch (true)
        {
            case Director::is_cli():
                $message .= PHP_EOL;
                break;

            default:
                $message .= '<br>';
                break;
        }

        echo $message;
    }

    public function run($request)
    {
        set_time_limit(0);
        switch ($request->getVar('type'))
        {
            case 'models':
                $this->migrateModels();
                break;

            case 'thumbs':
                // ImageThumbnailHelper::singleton()->run();
                $files = File::get();
                $assetAdmin = AssetAdmin::singleton();
                // set_time_limit(0);
                foreach ($files as $file) {
                    $assetAdmin->generateThumbnails($file, true);
                }
                self::echo("#### DONE ####");
                break;

            default:
                self::echo("Please specify a migration type:");
                foreach (self::TYPES as $type)
                {
                    self::echo("  - {$type}");
                }

                self::echo("");
                self::echo("nothing migrated...");
                self::echo("#### DONE ####");
                break;
        }
    }

    public function migrateModels()
    {
        foreach (self::MODEL_CLASSES as $old => $new)
        {
            $update = SQLUpdate::create($old);
            $update->addAssignments([
                '"ClassName"' => $new,
            ]);
            $update->execute();
            self::echo("Updated '{old}'");
        }

        self::echo("#### DONE ####");
    }
}
