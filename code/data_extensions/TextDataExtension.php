<?php
/**
 * Provides a couple of helper methods to Text classes.
 * 
 * Add this extension to a SiteTree instance
 * by adding this to your _config.php:
 * 
 * Object::add_extension('HTMLText', 'TextExtension');
 * 
 * @package cleanutilities
 * @subpackage data_extensions
 * 
 * @author arillo
 */
class TextDataExtension extends DataExtension {
	
	/**
	 * Shortens (html) text to a given $limit and appends $add to it.
	 * 
	 * @param int $limit
	 * @param string $add
	 * @return string
	 */
	public function SummaryHTML($limit=100, $add = "&hellip;") {
		$m = 0;
		$addEplisis = '';
		$returnstr = '';
		$returnArray = array();
		$html = array();
		$chars = preg_split('/(<[^>]*[^\/]>| )/i', $this->owner->value, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		foreach ($chars as $elemnt) {
			// found start tag
			if (preg_match('/^<(p|h1|h2|h3|h4|h5|h6|q|b|i|strong|em)(.*)>$/', $elemnt)) {
				preg_match('/^<(p|h1|h2|h3|h4|h5|h6|q|b|i|strong|em)(.*)>$/', $elemnt, $matches);
				array_push($html, $matches[1]);// convert <p class=""> to p
				array_push($returnArray, $elemnt);
			// found end tag
			} else if(preg_match('/^<\/(p|h1|h2|h3|h4|h5|h6|q|b|i|strong|em)(.*)>$/', $elemnt)) {
				preg_match('/^<\/(p|h1|h2|h3|h4|h5|h6|q|b|i|strong|em)(.*)>$/', $elemnt, $matches);
				$testelement = array_pop ($html);
				// match (ie: <p>etc</p>)
				if ($testelement==$elemnt[1]) array_pop($html);
				array_push($returnArray, $elemnt);
			} else {
				// done
				if ($elemnt == ' ') continue;
				array_push($returnArray, $elemnt);
				$m++;
				if ($m > $limit) {
					$addEplisis = $add;
					break;
				}
			}
		}
		// convert start tags to end tags
		$tmpr = '';
		foreach ($html as $elemnt) {
			$tmpr.='</'.$elemnt.'>';
		}
		return implode($returnArray, ' ') . $addEplisis . $tmpr;
	}
	
	/**
	 * Converts a given text into uft8 and shortens ist to $limit.
	 * Caution: Dont't use it with HTMLText instances.
	 * 
	 * @param int $limit
	 * @return string
	 */
	public function ConvertPlainTextToUTF8($limit = 0) {
		$text = trim($this->owner->value);
		if ($limit > 0) {
			$text = substr($text, 0, $limit);
		}
		return utf8_encode($text);
	}
	
	/**
	 * Converts a given text into uft8 and 
	 * shortens it by $limit and adds $add.
	 * Caution: Dont't use it with HTMLText instances.
	 * 
	 * @param int $limit
	 * @param string $add
	 * @return string
	 */
	public function LimitPlainTextToUTF8($limit = 0, $add = "...") {
		$value = $this->owner->ConvertPlainTextToUTF8($limit);
		$result = (strlen(trim($this->owner->value)) > $limit && $limit != 0) ? utf8_encode($value . $add) : $value;
		return $result;
	}
	
	/**
	 * Returns a representation of this text
	 * with all email addresses converted into html character entities.
	 * 
	 * @return string
	 */
	public function EmailObfuscated() {
		$content = $this->owner->forTemplate();
		$emailPattern = "/[A-Za-z0-9_-]+@[A-Za-z0-9_-]+\.([A-Za-z0-9_-][A-Za-z0-9_]+)/";
		if (preg_match_all($emailPattern, $content, $matches)) {
			$searches = array(); 
			$replaces = array(); 
			for ($i=0; $i<count($matches[0]); $i++) {
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
	 * @return bool
	 */
	function MoreWordsThan($numWords = 23) {
		$value = trim(Convert::xml2raw($this->owner->value));
		return (count(explode(' ', $value)) > $numWords);
	}
}
