<?php
/**
 * Provides a couple of helper methods to Text classes.
 *
 * Add this extension to a SiteTree instance
 * by adding this to your _config.php:
 *
 * Object::add_extension('HTMLText', 'TextDecorator');
 *
 * @package cleanutilities
 * @subpackage decorators
 *
 * @author arillo
 */
class TextDecorator extends DataObjectDecorator{

	/**
	 * Shortens (html) text to a given $limit and appends $add to it.
	 *
	 * @param int $limit
	 * @param string $add
	 * @return string
	 */
	public function SummaryHTML($limit=100, $add = "&hellip;"){
		$m = 0;
		$addEplisis = '';
		$returnstr = '';
		$returnArray = array();
		$html = array();
		$chars = preg_split('/(<[^>]*[^\/]>| )/i', $this->owner->value, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		foreach($chars as $elemnt){
			// found start tag
			if(preg_match('/^<(p|h1|h2|h3|h4|h5|h6|q|b|i|strong|em)(.*)>$/', $elemnt)){
				preg_match('/^<(p|h1|h2|h3|h4|h5|h6|q|b|i|strong|em)(.*)>$/', $elemnt, $matches);
				array_push($html, $matches[1]);// convert <p class=""> to p
				array_push($returnArray, $elemnt);
			// found end tag
			}else if(preg_match('/^<\/(p|h1|h2|h3|h4|h5|h6|q|b|i|strong|em)(.*)>$/', $elemnt)){
				preg_match('/^<\/(p|h1|h2|h3|h4|h5|h6|q|b|i|strong|em)(.*)>$/', $elemnt, $matches);
				$testelement = array_pop ($html);
				// match (ie: <p>etc</p>)
				if($testelement==$elemnt[1]) array_pop($html);
				array_push($returnArray, $elemnt);
			 }else{
				// done
				if($elemnt == ' ') continue;
				array_push($returnArray, $elemnt);
				$m++;
				if($m > $limit) {
					$addEplisis = $add;
					break;
				}
			}
		}
		// convert start tags to end tags
		$tmpr = '';
		foreach($html as $elemnt){
			$tmpr.='</'.$elemnt.'>';
		}
		return implode($returnArray, ' ') . $addEplisis . $tmpr;
	}

	/**
	 * Converts a given text into uft8 and shortens ist to $limit.
	 *
	 * @param int $limit
	 * @return string
	 */
	public function ConvertText($limit = 20){
		return utf8_encode(substr($this->owner->value, 0, $limit));
	}

	/**
	 * Converts a given text into uft8 and
	 * shortens it by $limit and adds $add.
	 *
	 * @param int $limit
	 * @param string $add
	 * @return string
	 */
	public function LimitCharactersUTF($limit = 20, $add = "..."){
		$value = trim($this->owner->value);
		$result = (strlen($value) > $limit) ? utf8_encode(substr(utf8_decode($value), 0, $limit)) . $add : $value;
		return $result;
	}


	/**
	 * Returns a representation of this text
	 * with all email addresses converted into html character entities.
	 *
	 * @return string
	 */
	public function EmailObfuscated(){
		$content = $this->owner->forTemplate();
		$emailPattern = "/[A-Za-z0-9_-]+@[A-Za-z0-9_-]+\.([A-Za-z0-9_-][A-Za-z0-9_]+)/";
		if(preg_match_all($emailPattern, $content, $matches)){
			$searches = array();
			$replaces = array();
			for($i=0; $i<count($matches[0]); $i++){
				$link = $matches[0][$i];
				$obfuscatedLink = CleanUtils::html_obfuscate($link);
				array_push($searches,$link);
				array_push($replaces, $obfuscatedLink);
			}
			$content = str_replace($searches, $replaces, $content);
		}
		return $content;
	}

	/**
	 * Tests if the text is longer than $numWords.
	 *
	 * @param int $numWords
	 * @return ArrayData
	 */
	function CheckWordCount($numWords = 26){
		$this->owner->value = trim(Convert::xml2raw($this->owner->value));
		$ret = explode(' ', $this->owner->value, $numWords + 1);
		if(count($ret) <= $numWords - 1) $flag = false;
		else $flag = true;

		return new ArrayData(array(
			'More' => $flag
		));
	}
}
