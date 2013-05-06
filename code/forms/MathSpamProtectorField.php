<?php
/**
 *  MathProtectorField: basically a TextField copy with a
 *  fixed maxlength setting, that validates against the
 *  given MathSpamProtection setting
 *
 *  20-09-2010
 *  @author: M. Bloem, Balbus Design
 */
class MathSpamProtectorField extends SpamProtectorField {

  /**
   * Creates an input field, class="text" and type="text"
   * with a 'fixed label' to which the current
   * MathSpamProtection question is added.
   */
	function __construct($name, $title = null, $value = "", $form = null){
		// add the MathSpamProtection question
		if (!empty($title)) $title .= ': ';
		$title .= MathSpamProtection::getMathQuestion();
		parent::__construct($name, $title, $value, $form);
	}


  /*
   *  These values are copied from the TextField class,
   *  where only the maxlength and size settings are different
   */
	function Field() {
		$attributes = array(
			'type' => 'text',
			'class' => 'text' . ($this->extraClass() ? $this->extraClass() : ''),
			'id' => $this->id(),
			'name' => $this->Name(),
			'value' => $this->Value(),
			'tabindex' => $this->getTabIndex(),
			'maxlength' => 2,
			'size' => 30
		);
		return $this->createTag('input', $attributes);
	}

  /*
   *  Validation
   *  @TODO does this work for Ajax forms?
   */
	function validate($validator) {
		$this->value = trim($this->value);
		if(!$this->value || !MathSpamProtection::correctAnswer($this->value)) {
			$validator->validationError($this->name, _t('MathSpamProtectionField.VALIDATION', 'Please enter a correct answer to this question (numbers only).'), 'validation');
		}
	}
}