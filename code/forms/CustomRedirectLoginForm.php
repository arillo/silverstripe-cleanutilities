<?php
/**
 * A MemberLoginForm which performs a group based redirect.
 * 
 * @package cleanutilities
 * @subpackage forms
 *
 * Make this work by adding
 * this to your _config.php:
 * 
 * Object::useCustomClass('MemberLoginForm', 'CustomRedirectLoginForm');
 * 
 * @author arillo
 */
class CustomRedirectLoginForm extends MemberLoginForm {
	
	/**
	 * Overidden, added call to redirectByGroup().
	 * 
	 * Login in the user and figure out where to redirect the browser.
	 *
	 * The $data has this format
	 * array(
	 *   'AuthenticationMethod' => 'MemberAuthenticator',
	 *   'Email' => 'sam@silverstripe.com',
	 *   'Password' => '1nitialPassword',
	 *   'BackURL' => 'test/link',
	 *   [Optional: 'Remember' => 1 ]
	 * )
	 *
	 *
	 * @param array $data
	 * @return void
	 */
	protected function logInUserAndRedirect($data) {
		
		Session::clear('SessionForms.MemberLoginForm.Email');
		Session::clear('SessionForms.MemberLoginForm.Remember');

		if (Member::currentUser()->isPasswordExpired()) {
			if (isset($_REQUEST['BackURL']) && $backURL = $_REQUEST['BackURL']) {
				Session::set('BackURL', $backURL);
			}
			$cp = new ChangePasswordForm($this->controller, 'ChangePasswordForm');
			$cp->sessionMessage('Your password has expired. Please choose a new one.', 'good');
			return $this->controller->redirect('Security/changepassword');
		}
		
		// Absolute redirection URLs may cause spoofing
		if (isset($_REQUEST['BackURL']) && $_REQUEST['BackURL'] && Director::is_site_url($_REQUEST['BackURL']) ) {
			return $this->controller->redirect($_REQUEST['BackURL']);
		}

		// Spoofing attack, redirect to homepage instead of spoofing url
		if (isset($_REQUEST['BackURL']) && $_REQUEST['BackURL'] && !Director::is_site_url($_REQUEST['BackURL'])) {
			return $this->controller->redirect(Director::absoluteBaseURL());
		}
		
		// If a default login dest has been set, redirect to that.
		if (Security::default_login_dest()) {
			return $this->controller->redirect(Director::absoluteBaseURL() . Security::default_login_dest());
		}

		// redirect by group
		if (singleton('Group')->hasExtension('GroupLoginDataExtension')){
			$this->redirectByGroup();
		}
		
		// Redirect the user to the page where he came from
		$member = Member::currentUser();
		if ($member) {
			$firstname = Convert::raw2xml($member->FirstName);
			if (!empty($data['Remember'])) {
				Session::set('SessionForms.MemberLoginForm.Remember', '1');
				$member->logIn(true);
			} else {
				$member->logIn();
			}

			Session::set(
				'Security.Message.message',
				_t('Member.WELCOMEBACK',
					"Welcome Back, {firstname}",
					array('firstname' => $firstname)
				)
			);
			Session::set("Security.Message.type", "good");
		}
		Controller::curr()->redirectBack();
	}
	
	protected function redirectByGroup() {
		if ($member = Member::currentUser()) {
			$groups = Group::get();
			foreach ($groups as $group) {
				if ($member->inGroup($group)  && $group->GoToAdmin == true ) {
					return $this->controller->redirect(Director::absoluteBaseURL() . "/admin");
				} else if ($member->inGroup($group) && $group->LinkPageID != 0) {
					if ($page = SiteTree::get()->byID($group->LinkPageID)) {
						return $this->controller->redirect($page->Link());
					}
				}
			}
		}
		return;
	}
}
