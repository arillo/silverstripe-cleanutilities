<?php
/**
 * A MemberLoginForm which performs a group based redirect. Works in conjunction with the GroupLoginDecorator.
 *
 * @package cleanutilities
 * @subpackage forms
 *
 * @author arillo
 */
class CustomLoginForm extends MemberLoginForm{

	/**
	 * Override of dologin
	 *
	 * @param array $data
	 */
	public function dologin($data){
		if($this->performLogin($data)){
		        if(!$this->redirectByGroup($data)) parent::dologin($data);
		}else{
			if($badLoginURL = Session::get("BadLoginURL")) Director::redirect($badLoginURL);
			else Director::redirectBack();
		}
	}

	/**
	 * Performs a group based redirect, if possible.
	 *
	 * @param array $data
	 * @return bool
	 */
	public function redirectByGroup($data){
		if(isset($_REQUEST['BackURL'])) return false;

		// gets the current member that is logging in.
		$member = Member::currentUser();
		// gets all the groups.
		$Groups = DataObject::get("Group");

		//cycle through each group
		foreach($Groups as $Group){
			//if the member is in the group and that group has GoToAdmin checked
			if($member->inGroup($Group->ID) && $Group->GoToAdmin == 1){
				//redirect to the admin page
	 			Director::redirect(Director::baseURL() . 'admin' );
				return true;
			}
			//otherwise if the member is in the group and that group has a page link defined
			else if($member->inGroup($Group->ID)  && $Group->LinkPageID != 0){
				//Get the page that is referenced in the group
				$Link = DataObject::get_by_id("SiteTree", "{$Group->LinkPageID}")->URLSegment;
				//direct to that page
				Director::redirect(Director::baseURL() . $Link);
				return true;
			}
		}
		//otherwise if none of the above worked return fase
		return false;
	}
}
class CustomLoginForm_Controller extends Page_Controller{}