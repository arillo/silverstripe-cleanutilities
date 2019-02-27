<?php
namespace Arillo\CleanUtilities\Models;

use SilverStripe\ORM\Dataobject;
use SilverStripe\Assets\File;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\AssetAdmin\Forms\UploadField;

use SilverStripe\Control\{
    Controller,
    Director
};

use SilverStripe\Forms\{
    FieldList,
    TextField,
    CheckboxField,
    DropdownField,
    LiteralField,
    TabSet
};

/**
 * A DataObject for Videos
 * Provides a video that can either be embeded through
 * a service api or through its own embed code.
 *
 * @todo needs new video file player
 *
 * @package cleanutilities
 * @subpackage models
 *
 * @author arillo
 */
class CleanVideo extends DataObject
{
    private static $table_name = 'CleanVideo';

    private static $db = array(
        "Title" => "Text",
        "VideoAddress" => "Text",
        "VideoType" => "Enum('Embed, File','Embed')",
        "Autoplay" => "Boolean"
    );

    private static $has_one = array(
        'Reference' => SiteTree::class,
        'VideoFile' => File::class
    );

    /**
     * Allowed file extensions for uploading.
     * @var array
     */
    private static $allowed_extensions = array('mov', 'flv', 'mp4');

    /**
     * Specifies a custom upload folder name.
     * @var string
     */
    private static $upload_folder = 'Videos';

    private static $use_video_upload = true;

    /**
     * Checks if the given $media_url is a playable one.
     *
     * @param string $media_url
     * @return bool
     */
    public static function validate_video($media_url)
    {
        $infos = self::prepare_url($media_url);
        if ($infos['sourcetype'] != 'error') {
            return true;
        }
        return false;
    }

    /**
     * Generates the fiting embed code for the video
     * according to its service.
     *
     * @param string $media_url
     * @param int $width
     * @param int $height
     * @param bool $autoplay
     * @return string
     */
    public static function video_embed(
        $media_url,
        $width = 400,
        $height = 300,
        $autoplay = false
    ) {
        $infos = self::prepare_url($media_url);
        if ($infos['sourcetype'] != 'error')
        {
            switch ($infos['sourcetype'])
            {
                case 'youtube':
                    if ($autoplay) {
                        $ap = 'true';
                    } else {
                        $ap = 'false';
                    }
                    return '<iframe src="http://www.youtube.com/embed/'.$infos['sourceid'].'?wmode=opaque&autoplay='.$autoplay.'" width="'. $width .'" height="'. $height .'" frameborder="0"></iframe>';

                case 'vimeo':
                    if ($autoplay) {
                        $ap = 'true';
                    } else {
                        $ap = 'false';
                    }
                    return '<iframe src="http://player.vimeo.com/video/'.$infos['sourceid'].'?wmode=transparent&autoplay='.$autoplay.'" width="'. $width .'" height="'. $height .'" frameborder="0"></iframe>';

                case 'metacafe':
                    if ($autoplay) {
                        $ap = 'yes';
                    } else {
                        $ap = 'no';
                    }
                    return '<iframe flashVars="playerVars=autoPlay='.$ap.'" src="'.$infos['media_url'].'?playerVars=autoPlay='.$ap.'" width="'.$width.'" height="'.$height.'"</iframe>';
//                  return '<embed flashVars="playerVars=autoPlay='.$ap.'" src="'.$infos['media_url'].'" width="'.$width.'" height="'.$height.'" wmode="transparent" allowFullScreen="true" allowScriptAccess="always" name="Metacafe_8409457" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed>';

                case 'dailymotion':
                    if ($autoplay) {
                        $ap = '1';
                    } else {
                        $ap = '0';
                    }
                    return '<iframe frameborder="0" width="'.$width.'" height="'.$height.'" src="http://www.dailymotion.com/embed/video/'.$infos['sourceid'].'?autoPlay='.$autoplay.'"></iframe>';

                case 'facebook':
//                  if($autoplay) $ap = '1';
//                  else $ap = '0';
                    return '<iframe width="516" height="346" frameborder="0" src="http://www.facebook.com/v/'.$infos['sourceid'].'"></iframe>';

                default:
                    return '
                    <object type="application/x-shockwave-flash" data="'.$infos['media_url'].'?wmode=transparent" width="'. $width .'" height="'. $height .'">
                    <param name="movie" value="'.$infos['media_url'].'"/>
                    <param name="wmode" value="opaque" />
                    <param name="allowfullscreen" value="true" /><span  name="allowscriptaccess" value="always"/>
                    </object>';
            }
        }
    }

    /**
     * Tests if a given url is a youtube video url.
     *
     * @param string $media
     * @return bool
     */
    public static function is_youtube($url)
    {
        $urls = parse_url($url);
        //expect url is http://youtu.be/abcd, where abcd is video iD
        if (isset($urls['host']))
        {
            if ($urls['host'] == 'youtu.be' || $urls['host'] == 'www.youtu.be')
            {
                return true;
            }

            if ((preg_match('/v=([^(\&|$)]*)/i', $url, $match)
                || preg_match('/v\/([^(\&|$)]*)/i', $url, $match)
                || preg_match('/video_id=([^(\&|$)]*)/i', $url, $match))
                && ($urls['host'] == 'youtube.com' || $urls['host'] == 'www.youtube.com')
            ) {
                return true;
            }
        }
        return false;
    }

    public static function is_vimeo($url)
    {
        $urls = parse_url($url);
        if (isset($urls['host']))
        {
            return ($urls['host'] == 'vimeo.com' || $urls['host'] == 'www.vimeo.com');
        }

        return false;
    }

    /**
     * Splits the given $media url into its logical parts.
     *
     * @param string $media
     * @return array
     */
    public static function prepare_url($media)
    {
        $sourcetype = 'error';
        $sourceid ='error';
        $media_url = 'error';

        $urls = parse_url($media);
        //expect url is http://youtu.be/abcd, where abcd is video iD
        if (isset($urls['host'])) {
            if ($urls['host'] == 'youtu.be'
                || $urls['host'] == 'www.youtu.be'
            ) {
                $sourceid = ltrim($urls['path'], '/');
                $sourcetype = 'youtube';
                $media_url =  'http://www.youtube.com/v/' . $sourceid;
            }
            // Facebook
            elseif (preg_match('/facebook.com\/video\/video.php\?v=([^(\&|$)]*)/i', $media, $match)
                    || preg_match('/facebook.com\/video\/#\/video\/video.php\?v=([^(\&|$)]*)/i', $media, $match)
            ) {
                $media_url = 'http://www.facebook.com/v/' . $match[1] . '';
                $sourcetype = 'facebook';
                $sourceid = $match[1];
            }
            // Youtube
            elseif ((preg_match('/v=([^(\&|$)]*)/i', $media, $match)
                    || preg_match('/v\/([^(\&|$)]*)/i', $media, $match)
                    || preg_match('/video_id=([^(\&|$)]*)/i', $media, $match))
                    && ($urls['host'] == 'youtube.com'
                    || $urls['host'] == 'www.youtube.com')
            ) {
                $media_url =  'http://www.youtube.com/v/' . $match[1];
                $sourcetype = 'youtube';
                $sourceid = $match[1];
            }
            // Dailymotion
            elseif (preg_match('/video\/([^(\&|$)]*)/i', $media, $match)
                    && ($urls['host'] == 'dailymotion.com'
                    || $urls['host'] == 'www.dailymotion.com')
            ) {
                $dialymotion_video = explode('_', $match[1]);
                $media_url = 'http://www.dailymotion.com/swf/' . $dialymotion_video[0] . '&related=0';
                $sourcetype = 'dailymotion';
                $sourceid = $dialymotion_video[0];
            }
            // Metacafe
            elseif ((preg_match('/watch\/(\d+)\//i', $media, $match)
                    || preg_match('/fplayer\/(\d+)/i', $media, $match))
                    && ($urls['host'] == 'metacafe.com'
                    || $urls['host'] == 'www.metacafe.com')
            ) {
                $partes = explode('/', $media);
                $media_url = 'http://www.metacafe.com/fplayer/' . $match[1] . '/' . $partes[5] . '.swf';
                $sourcetype = 'metacafe';
                $sourceid = $match[1];
            }
            // Vimeo
            elseif ($urls['host'] == 'vimeo.com'
                    || $urls['host'] == 'www.vimeo.com'
            ) {
                $vimeo_ID = substr($urls['path'], 1);
                $media_url = 'http://www.vimeo.com/moogaloop.swf?clip_id=' . $vimeo_ID;
                $sourcetype = 'vimeo';
                $sourceid = $vimeo_ID;
            }
            // If not match, show error media type
            else {
                return array(
                    'sourcetype' => 'error',
                    'sourceid' => 'error',
                    'media_url' => 'error'
                );
            }
            return array(
                'sourcetype' => $sourcetype,
                'sourceid' => $sourceid,
                'media_url' => $media_url
            );
        } else {
            return array(
                'sourcetype' => 'error',
                'sourceid' => 'error',
                'media_url' => 'error'
            );
        }
    }

    /**
     * Returns the actual video embed code.
     * If $autoplay isset it will use this value
     * instead of the value stored in DB.
     *
     * @param int $width
     * @param int $height
     * @param boolean $autoplay
     * @return string
     */
    public function VideoEmbed($width = 640, $height = 375, $autoplay = null)
    {
        $autoplay = isset($autoplay) ? $autoplay : $this->Autoplay;
        switch ($this->VideoType) {
            case 'Embed':
                if ($this->VideoAddress!='') {
                    return self::video_embed(
                        $this->VideoAddress,
                        $width,
                        $height,
                        $autoplay
                    );
                }
                break;
            case 'File':
                // if ($this->VideoFileID != 0 && isset($this->VideoFileID)) {
                //     $vars = array(
                //         'ID' => $this->ID,
                //         'Width' => $width,
                //         'Height' => $height,
                //         'VideoURL' => $this->VideoFile()->URL,
                //         'ThemeDir' => $this->ThemeDir(),
                //         'Autoplay' => $autoplay,
                //         'Module' => CleanUtils::$module
                //     );
                //     Requirements::javascript(CleanUtils::$module . "/javascript/libs/swfobject.js");
                //     Requirements::javascriptTemplate(CleanUtils::$module . "/javascript/init_video.js", $vars);
                //     return $this->customise($vars)->renderWith(array('VideoEmbed'));
                // }
                break;
        }
        return false;
    }

    public function VideoThumb($width = 240, $height = 180)
    {
        if (self::is_youtube($this->VideoAddress))
        {
            return $this
                ->customise([
                    'Width' => $width,
                    'Height' => $height,
                    'Src' => "http://img.youtube.com/vi/{$this->VideoId()}/0.jpg",
                ])
                ->renderWith(__CLASS__ . '_VideoThumb')
            ;
            // return '<img width="'.$width.'px" height="'.$height.'px" src="http://img.youtube.com/vi/'.$this->VideoId().'/0.jpg"/>';
        }

        if (self::is_vimeo($this->VideoAddress))
        {
            $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$this->VideoId().".php"));
            $src = $hash[0]['thumbnail_medium'];
            return '<img width="240px" height="180px" src="'.$src.'"/>';
        }
        return false;
    }

    public function getCMSFields()
    {
        $fields = FieldList::create(TabSet::create('Root'));
        $fields->addFieldToTab("Root.Main", LiteralField::create('VideoError', '<div></div>'));
        $fields->addFieldToTab("Root.Main", CheckboxField::create(
            'Autoplay',
            _t('CleanVideo.AUTOPLAY', 'Auto play')
        ));

        if (self::$use_video_upload) {
            $fields->addFieldToTab("Root.Main",
                DropdownField::create(
                    'VideoType',
                    _t('CleanVideo.BEHAVIOUR', 'Choose a behaviour'),
                    $this->dbObject('VideoType')->enumValues()
                )
            );
        } else {
            $fields->addFieldToTab("Root.Main", new HiddenField("VideoType", "VideoType", "Embed"));
        }
        $fields->addFieldToTab("Root.Main", TextField::create(
            'Title',
            _t('CleanUtilities.TITLE', 'Title')
        ));
        $fields->addFieldToTab("Root.Main", TextField::create(
            'VideoAddress',
            _t('CleanVideo.VIDEO_URL', 'Video URL')
        ));
        if (self::$use_video_upload) {
            $fields->addFieldToTab("Root.Main", $upload = UploadField::create(
                'VideoFile',
                _t('CleanVideo.VIDEO_FILE', 'Video File')
            ));
            $upload->getValidator()->setAllowedExtensions(self::$allowed_extensions);
            if ($this->hasExtension('ControlledFolderDataExtension')) {
                $upload->setFolderName($this->getUploadFolder());
            } else {
                $upload->setFolderName(self::$upload_folder);
            }
        }
        $this->extend('updateCMSFields', $fields);
        return $fields;
    }

    /**
     * Getter for the video file name
     *
     * @return string
     */
    public function getVideoFileName()
    {
        if ($this->VideoFileID != 0 && isset($this->VideoFileID)) {
            return $this->VideoFile()->FileName;
        }
        return '';
    }

    /**
     * Getter for the service's video id.
     *
     * @return string
     */
    public function VideoId()
    {
        $purl = self::prepare_url($this->VideoAddress);
        return $purl['sourceid'];
    }
    public function IsYoutube()
    {
        return self::is_youtube($this->VideoAddress);
    }
}
