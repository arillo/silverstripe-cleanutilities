<?php
/**
 * Provides notification/flash messages to SiteTree classes.
 *
 * Add this extension to a SiteConfig instance
 * by adding this to your _config.php:
 * 
 * Object::add_extension('Page', 'NotificationDataExtension');
 *
 * @package cleanutilities
 * @subpackage data_extensions
 * 
 * @author arillo
 */
class NotificationDataExtension extends DataExtension
{

    /**
     * Session variable name
     * @var string
     */
    public static $session_store = 'CleanUtilities.Notifications';
    
    /**
     * Adds a message $str to notifications with a certain $mode.
     * This static version of this function makes this usable from everywhere.
     * 
     * @param string $str
     * @param string $mode
     */
    public static function set_session_message($str = '', $mode = 'success')
    {
        $sm = Session::get(self::$session_store);
        $ret = array();
        $ret['Msg'] = $str;
        $ret['Mode'] = $mode;
        if (isset($sm)) {
            $sm[] = $ret;
        } else {
            $sm = array();
            $sm[] = $ret;
        }
        Session::set(self::$session_store, $sm);
    }

    /**
     * Adds a message $str to notifications with a certain $mode.
     *  
     * @param string $str
     * @param string $mode
     */
    public function setSessionMessage($str = '', $mode = 'success')
    {
        self::set_session_message($str, $mode);
    }

    /**
     * Indicates if current notifications do exist.
     * 
     * @return bool
     */
    public function HaveMessages()
    {
        $msgs = Session::get(self::$session_store);
        return isset($msgs);
    }
    
    /**
     * Returns all current notifications.
     * 
     * @return ArrayList
     */
    public function SessionMessages()
    {
        $msgs = Session::get(self::$session_store);
        Session::clear(self::$session_store);
        if (is_array($msgs)) {
            return new ArrayList(
                array_map(function ($item) {
                    return new ArrayData($item);
                },
                $msgs)
            );
        }
    }
}
