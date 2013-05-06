<?php
/**
 * Provides helper funtionality to handle and display videos
 * from diffrent media platforms. Supported platforms are:
 * -> Youtube
 * -> Vimeo
 * -> Metacafe
 * -> Dailymotion
 * -> Facebook
 * 
 * @package cleanutilities
 * @subpackage system
 * 
 * @author arillo
 */
class VideoUtility{
	
	/**
	 * Checks if the given $media_url is a playable one.
	 * 
	 * @param string $media_url
	 * @return bool
	 */
	public static function validate_video($media_url){
		$infos = VideoUtility::prepare_url($media_url);
		if($infos['sourcetype'] != 'error') return true;
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
	public static function video_embed($media_url, $width = 400, $height = 300, $autoplay = false){
		$infos = VideoUtility::prepare_url($media_url);
		if($infos['sourcetype'] != 'error'){
			switch($infos['sourcetype']){
				case 'youtube':
				if($autoplay) $ap = 'true';
					else $ap = 'false';
					return '<iframe src="http://www.youtube.com/embed/'.$infos['sourceid'].'?wmode=opaque&autoplay='.$autoplay.'" width="'. $width .'" height="'. $height .'" frameborder="0"></iframe>';
				case 'vimeo':
					if($autoplay) $ap = 'true';
					else $ap = 'false';
					return '<iframe src="http://player.vimeo.com/video/'.$infos['sourceid'].'?wmode=transparent&autoplay='.$autoplay.'" width="'. $width .'" height="'. $height .'" frameborder="0"></iframe>';      
				case 'metacafe':
					if($autoplay) $ap = 'yes';
					else $ap = 'no';
					return '<iframe flashVars="playerVars=autoPlay='.$ap.'" src="'.$infos['media_url'].'?playerVars=autoPlay='.$ap.'" width="'.$width.'" height="'.$height.'"</iframe>';
//					return '<embed flashVars="playerVars=autoPlay='.$ap.'" src="'.$infos['media_url'].'" width="'.$width.'" height="'.$height.'" wmode="transparent" allowFullScreen="true" allowScriptAccess="always" name="Metacafe_8409457" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed>';
				case 'dailymotion':
					if($autoplay) $ap = '1';
					else $ap = '0';
					return '<iframe frameborder="0" width="'.$width.'" height="'.$height.'" src="http://www.dailymotion.com/embed/video/'.$infos['sourceid'].'?autoPlay='.$autoplay.'"></iframe>';
				case 'facebook':
//					if($autoplay) $ap = '1';
//					else $ap = '0';
					return '<iframe width="516" height="346" frameborder="0" src="http://www.facebook.com/v/'.$infos['sourceid'].'"></iframe>';
				default:
					return '
					<object type="application/x-shockwave-flash" data="'.$infos['media_url'].'?wmode=transparent" width="'. $width .'" height="'. $height .'">
					<param name="movie"	value="'.$infos['media_url'].'"/>
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
	public static function is_youtube($media){
		$urls = parse_url($media);
	    //expect url is http://youtu.be/abcd, where abcd is video iD
	    if(isset($urls['host'])){
	    	if($urls['host'] == 'youtu.be' || $urls['host'] == 'www.youtu.be') return true;
			if((preg_match('/v=([^(\&|$)]*)/i', $media, $match) 
				|| preg_match('/v\/([^(\&|$)]*)/i', $media, $match) 
				|| preg_match('/video_id=([^(\&|$)]*)/i', $media, $match)) 
				&& ($urls['host'] == 'youtube.com' || $urls['host'] == 'www.youtube.com')){
				return true;	
			}
		}
		return false;
	}
	
	/**
	 * Splits the given $media url into its logical parts.
	 * 
	 * @param string $media
	 * @return array
	 */
	public static function prepare_url($media){
		$sourcetype = 'error';
		$sourceid ='error';
		$media_url = 'error';
		
		$urls = parse_url($media);
		//expect url is http://youtu.be/abcd, where abcd is video iD
		if(isset($urls['host'])){
			if($urls['host'] == 'youtu.be' || $urls['host'] == 'www.youtu.be'){
				$sourceid = ltrim($urls['path'],'/');
				$sourcetype = 'youtube';
				$media_url =  'http://www.youtube.com/v/' . $sourceid;
			}
			// Facebook
			else if(preg_match('/facebook.com\/video\/video.php\?v=([^(\&|$)]*)/i', $media, $match) || preg_match('/facebook.com\/video\/#\/video\/video.php\?v=([^(\&|$)]*)/i', $media, $match)){
				$media_url = 'http://www.facebook.com/v/' . $match[1] . '';
				$sourcetype = 'facebook';
				$sourceid = $match[1];
			}
			// Youtube
			else if((preg_match('/v=([^(\&|$)]*)/i', $media, $match) 
					|| preg_match('/v\/([^(\&|$)]*)/i', $media, $match) 
					|| preg_match('/video_id=([^(\&|$)]*)/i', $media, $match)) 
					&& ($urls['host'] == 'youtube.com' 
					|| $urls['host'] == 'www.youtube.com')){
				$media_url =  'http://www.youtube.com/v/' . $match[1];
				$sourcetype = 'youtube';
				$sourceid = $match[1];
			}
			// Dailymotion
			else if (preg_match('/video\/([^(\&|$)]*)/i', $media, $match) && ($urls['host'] == 'dailymotion.com' || $urls['host'] == 'www.dailymotion.com')){
				$dialymotion_video = explode('_', $match[1]);
				$media_url = 'http://www.dailymotion.com/swf/' . $dialymotion_video[0] . '&related=0';
				$sourcetype = 'dailymotion';
				$sourceid = $dialymotion_video[0];
			}
			// Metacafe
			else if((preg_match('/watch\/(\d+)\//i', $media, $match) 
					|| preg_match('/fplayer\/(\d+)/i', $media, $match)) 
					&& ($urls['host'] == 'metacafe.com' 
					|| $urls['host'] == 'www.metacafe.com')){
				$partes = explode('/', $media);
				$media_url = 'http://www.metacafe.com/fplayer/' . $match[1] . '/' . $partes[5] . '.swf';
				$sourcetype = 'metacafe';
				$sourceid = $match[1];
			}
			// Vimeo
			else if($urls['host'] == 'vimeo.com' || $urls['host'] == 'www.vimeo.com'){
				$vimeo_ID = substr($urls['path'], 1);
				$media_url = 'http://www.vimeo.com/moogaloop.swf?clip_id=' . $vimeo_ID;
				$sourcetype = 'vimeo';
				$sourceid = $vimeo_ID;
			}
			 // If not match, show error media type
			else{
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
		}else{
			return array(
				'sourcetype' => 'error',
				'sourceid' => 'error',
				'media_url' => 'error'
			);
		}
	}
}
