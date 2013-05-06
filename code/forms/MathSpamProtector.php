<?php
/**
 *  MathProtector: The protector implements MathSpamProtection
 *  by returning the required FormField (the MathProtectorField)
 *
 *  20-09-2010
 *  @author: M. Bloem, Balbus Design
 */
class MathSpamProtector implements SpamProtector {

 /**
  * Return the Field that we will use in this protector
  *
  * @return MathProtectorField
  */
	function getFormField($name = 'Math', $title = '', $value = null, $form = null, $rightTitle = null) {
		$title = _t('MathSpamProtector.SPAMQUESTION', 'MathSpam protection');
		return new MathSpamProtectorField($name, $title, $value, $form, $rightTitle);
	}


 /**
  *  Function required to handle dynamic feedback of the system.
  *  if unneeded just return true
  *
  *  @return true
  */
	public function sendFeedback($object = null, $feedback = ""){
		return true;
	}
}