<?php
require_once 'Zend/Date.php';
class DateDropdownField extends DateField
{
    private static $default_config = array(
        'showcalendar' => true,
        'jslocale' => null,
        'dmyfields' => false,
        'dmyseparator' => '&nbsp;<span class="separator">/</span>&nbsp;',
        'dmyplaceholders' => true,
        'dateformat' => null,
        'yearRange' => "-60:+0",
        'datavalueformat' => 'yyyy-MM-dd',
        'min' => null,
        'max' => null,
    );

    public function FieldHolder($properties = array())
    {
        if ($this->getConfig('showcalendar')) {
            // TODO Replace with properly extensible view helper system 
            $d = DateDropdownField_View_JQuery::create($this);
            if (!$d->regionalSettingsExist()) {
                $dateformat = $this->getConfig('dateformat');

                // if no localefile is present, the jQuery DatePicker 
                // month- and daynames will default to English, so the date
                // will not pass Zend validatiobn. We provide a fallback  
                if (preg_match('/(MMM+)|(EEE+)/', $dateformat)) {
                    $this->setConfig('dateformat', $this->getConfig('datavalueformat'));
                }
            }
            $d->onBeforeRender();
        }
        $html = parent::FieldHolder();

        if (!empty($d)) {
            $html = $d->onAfterRender($html);
        }
        return $html;
    }
    
    public function SmallFieldHolder($properties = array())
    {
        $d = DateDropdownField_View_JQuery::create($this);
        $d->onBeforeRender();
        $html = parent::SmallFieldHolder($properties);
        $html = $d->onAfterRender($html);
        return $html;
    }

    public function Field($properties = array())
    {
        $config = array(
            'showcalendar' => $this->getConfig('showcalendar'),
            'isoDateformat' => $this->getConfig('dateformat'),
            'jquerydateformat' => DateDropdownField_View_JQuery::convert_iso_to_jquery_format($this->getConfig('dateformat')),
            'min' => $this->getConfig('min'),
            'max' => $this->getConfig('max'),
            'yearRange' => $this->getConfig('yearRange')
        );

        // Add other jQuery UI specific, namespaced options (only serializable, no callbacks etc.)
        // TODO Move to DateField_View_jQuery once we have a properly extensible HTML5 attribute system for FormField
        $jqueryUIConfig = array();
        foreach ($this->getConfig() as $k => $v) {
            if (preg_match('/^jQueryUI\.(.*)/', $k, $matches)) {
                $jqueryUIConfig[$matches[1]] = $v;
            }
        }
        if ($jqueryUIConfig) {
            $config['jqueryuiconfig'] =  Convert::array2json(array_filter($jqueryUIConfig));
        }
        $config = array_filter($config);
        foreach ($config as $k => $v) {
            $this->setAttribute('data-' . $k, $v);
        }
        
        // Three separate fields for day, month and year
        if ($this->getConfig('dmyfields')) {
            // values
            $valArr = ($this->valueObj) ? $this->valueObj->toArray() : null;

            // fields
            $fieldNames = Zend_Locale::getTranslationList('Field', $this->locale);
            $fieldDay = NumericField::create($this->name . '[day]', false, ($valArr) ? $valArr['day'] : null)
                ->addExtraClass('day')
                ->setAttribute('placeholder', $this->getConfig('dmyplaceholders') ? $fieldNames['day'] : null)
                ->setMaxLength(2);

            $fieldMonth = NumericField::create($this->name . '[month]', false, ($valArr) ? $valArr['month'] : null)
                ->addExtraClass('month')
                ->setAttribute('placeholder', $this->getConfig('dmyplaceholders') ? $fieldNames['month'] : null)
                ->setMaxLength(2);
            
            $fieldYear = NumericField::create($this->name . '[year]', false, ($valArr) ? $valArr['year'] : null)
                ->addExtraClass('year')
                ->setAttribute('placeholder', $this->getConfig('dmyplaceholders') ? $fieldNames['year'] : null)
                ->setMaxLength(4);
            
            // order fields depending on format
            $sep = $this->getConfig('dmyseparator');
            $format = $this->getConfig('dateformat');
            $fields = array();
            $fields[stripos($format, 'd')] = $fieldDay->Field();
            $fields[stripos($format, 'm')] = $fieldMonth->Field();
            $fields[stripos($format, 'y')] = $fieldYear->Field();
            ksort($fields);
            $html = implode($sep, $fields);

            // dmyfields doesn't work with showcalendar
            $this->setConfig('showcalendar', false);
        }
        // Default text input field
        else {
            $html = parent::Field();
        }
        
        return $html;
    }
    
    public function performReadonlyTransformation()
    {
        $field = $this->castedCopy('DateDropdownField_Disabled');
        $field->setValue($this->dataValue());
        $field->readonly = true;
        
        return $field;
    }
}
/**
 * Disabled version of {@link DateField}.
 * Allows dates to be represented in a form, by showing in a user friendly format, eg, dd/mm/yyyy.
 * @package forms
 * @subpackage fields-datetime
 */
class DateDropdownField_Disabled extends DateDropdownField
{
    
    protected $disabled = true;
        
    public function Field($properties = array())
    {
        if ($this->valueObj) {
            if ($this->valueObj->isToday()) {
                $val = Convert::raw2xml($this->valueObj->toString($this->getConfig('dateformat'))
                    . ' ('._t('DateField.TODAY', 'today').')');
            } else {
                $df = new Date($this->name);
                $df->setValue($this->dataValue());
                $val = Convert::raw2xml($this->valueObj->toString($this->getConfig('dateformat'))
                    . ', ' . $df->Ago());
            }
        } else {
            $val = '<i>('._t('DateField.NOTSET', 'not set').')</i>';
        }
        
        return "<span class=\"readonly\" id=\"" . $this->id() . "\">$val</span>";
    }
    
    public function Type()
    {
        return "date_disabled readonly";
    }
    
    public function validate($validator)
    {
        return true;
    }
}
class DateDropdownField_View_JQuery extends DateField_View_JQuery
{
    public function onAfterRender($html)
    {
        if ($this->getField()->getConfig('showcalendar')) {
            Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
            Requirements::css(THIRDPARTY_DIR . '/jquery-ui-themes/smoothness/jquery-ui.css');
            Requirements::javascript(FRAMEWORK_DIR . '/thirdparty/jquery-ui/jquery-ui.js');
            
            // Include language files (if required)
            if ($this->jqueryLocaleFile) {
                Requirements::javascript($this->jqueryLocaleFile);
            }
            Requirements::javascript(CleanUtils::$module."/javascript/DateDropdownField.js");
            // Requirements::javascript(FRAMEWORK_DIR . "/javascript/DateField.js");
        }
        
        return $html;
    }
}
