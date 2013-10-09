<?php
/**
 * Provides notification/flash messages to SiteTree classes.
 *
 * Add this extension to a SiteConfig instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('Page', 'NotificationDecorator');
 *
 * @package cleanutilities
 * @subpackage decorators
 *
 * @author arillo
 */
class NotificationDecorator extends DataObjectDecorator{

	/**
	 * Adds a message $str to notifications with a certain $mode.
	 * This static version of this function makes this usable from everywhere.
	 *
	 * @param string $str
	 * @param string $mode
	 */
	public static function set_session_message($str = '', $mode = 'msgGood'){
		$sm = Session::get('SysMsg');
		$ret = array();
		$ret['Msg'] = $str;
		$ret['Mode'] = $mode;
		if(isset($sm)){
			$sm[] = $ret;
		}else{
			$sm = array();
			$sm[] = $ret;
		}
		Session::set('SysMsg',$sm);
	}

	/**
	 * Adds a message $str to notifications with a certain $mode.
	 *
	 * @param string $str
	 * @param string $mode
	 */
	public function setSessionMessage($str = '',$mode = 'msgGood'){ self::set_session_message($str, $mode); }

	/**
	 * Indicates if current notifications do exist.
	 *
	 * @return bool
	 */
	public function HaveMessages(){
		$msgs = Session::get('SysMsg');
		if(isset($msgs)) return true;

		return false;
	}

	/**
	 * Returns all current notifications.
	 *
	 * @return DataObjectSet
	 */
	public function SessionMessages(){
		$msgs = Session::get('SysMsg');
		$ret = array();
		$messages = array();
		foreach($msgs as $key => $msg) {
			if (array_search($msg['Msg'], $messages) === false) {
				$ret[] = $msg;
			}
			$messages[] = $msg['Msg'];
		}
		Session::clear('SysMsg');
		return new DataObjectSet($ret);
	}
}