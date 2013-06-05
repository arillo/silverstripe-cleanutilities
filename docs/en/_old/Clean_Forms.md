#Clean Forms
__written by Arillo__
 
A bunch of extra functionality for working with forms. Partly from third parties.

##CustomLoginForm
A MemberLoginForm which performs a group based redirect. Works together with [GroupLoginDecorator](Utilities_Decorators#grouplogindecorator).
###Install
	// in _config.php
	Object::useCustomClass('MemberLoginForm', 'CustomLoginForm');
	Object::add_extension('Group', 'GroupLoginDecorator');

###Public functions
#####dologin($data)
	/**
	 * Override of dologin
	 * 
	 * @param array $data
	 */
	public function dologin($data)
#####redirectByGroup($data)
	/**
	 * Performs a group based redirect, if possible.
	 * 
	 * @param array $data
	 * @return bool
	 */
	public function redirectByGroup($data)
##DateDropdownField
Date selector field using jquery.ui.datepicker. Very useful datpicker to acomplish date selection in the past, for example a birthday date.
###Usage
	$f = new DateDropdownField(
		"BirthDate",
		_t('Submission.BIRTHDATE','Geburtstag')
	);
	$f->setLocale('de_DE');
##MathSpamProtector
MathProtector: The protector implements MathSpamProtection by returning the required FormField (the MathProtectorField). 
In order to use this functionality we will have to include MathSpamProtector module. Go [here](http://www.balbuss.com/implementing-mathspamprotection/) for downloading the module.

__by: [M. Bloem, Balbus Design](http://www.balbuss.com/)__
###Usage
[See here](http://www.balbuss.com/implementing-mathspamprotection/)
##MathSpamProtectorField
MathProtectorField: basically a TextField copy with a fixed maxlength setting, that validates against the given MathSpamProtection setting

__by: [M. Bloem, Balbus Design](http://www.balbuss.com/)__
###Usage
[See here](http://www.balbuss.com/implementing-mathspamprotection/)
	

