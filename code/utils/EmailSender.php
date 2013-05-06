<?php
/**
 * Helper for sending emails with ease.
 * 
 * @package cleanutilities
 * @subpackage utils
 * 
 * @author arillo
 */
class EmailSender{
	
	/**
	 * Send an email with the give parameters.
	 * 
	 * @param string $from
	 * @param string|array $to
	 * @param string $subject
	 * @param string $template
	 * @param array $popdata
	 * @param File $file
	 * @return bool
	 */
	public static function send_email($from = '', $to = '', $subject = '', $template = '', $popdata = null, $file = null){	
		$valid = true;	
		if($subject != '') $encoded_subject='=?UTF-8?B?'.base64_encode($subject).'?=';
		else $valid = false;   
		if($from == '' || $template == '') $valid = false;
		if($valid){
			if(self::validate_email($to)){
				$email = new Email($from, $to, $encoded_subject, null);
				$email->setTemplate($template);
				if($popdata != null){
					$email->populateTemplate(array(
						'Data' => $popdata
					));	
				}
				if($file != null) $email->attachFile($file->FileName, $file->Name);
				return $email->send();
			}else{
				if(is_array($to)){
					if(count($to > 0)){
						foreach($to as $receiver){
							if(self::validate_email($receiver)){
								$email = new Email($from, $receiver, $encoded_subject, null);
								$email->setTemplate($template);
								if($popdata != null){
									$email->populateTemplate(array(
										'Data' => $popdata
									));	
								}
								if($file != null) $email->attachFile($file->FileName, $file->Name);
								$ve = $email->send();
								if(!$ve) $valid = false;
							}else $valid = false;
						}
					}else $valid = false;
					return $valid;
				}else{
					if($group = DataObject::get_one('Group',"Title = '".$to."'")){
						if($group->Members()->Count() > 0){
							foreach($group->Members() as $gmember){
								$email = new Email($from, $gmember->Email, $encoded_subject,null);
								$email->setTemplate($template);
								if($popdata != null){
									$email->populateTemplate(array(
										'Data' => $popdata
									));	
								}
								if($file != null) $email->attachFile($file->FileName, $file->Name);
								$ve = $email->send();
								if(!$ve) $valid = false;			
							}
						}else $valid = false;
					}else $valid = false;
					return $valid;
				}				
			}
		}
		return false;
	}
	
	/**
	 * Email address validity check.
	 * 
	 * @param string $email
	 * @return bool
	 */
	public static function validate_email($email = ''){
		if(is_array($email)) return false;
		$email = trim($email);
		$pcrePattern = '^[a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$';
		// PHP uses forward slash (/) to delimit start/end of pattern, so it must be escaped
		$pregSafePattern = str_replace('/', '\\/', $pcrePattern);
		if($email == '' || !preg_match('/' . $pregSafePattern . '/i', $email)) return false;
		
		return true;
	}
}