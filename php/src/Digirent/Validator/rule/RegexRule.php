<?php

require_once 'Digirent/Validator/Rule.php';

class RegexRule extends Digirent_Validator_Rule
{
    var $pattern;
    
    function RegexRule()
    {
        parent::Digirent_Validator_Rule();
    }

    function setPattern($value)
    {
        $this->pattern = $value;
    }

    function check($value)
    {
        $value = (string) $value;

        if ($value === '') { return true; }

        if (is_null($this->pattern)) {
            trigger_error(__CLASS__ . '::pattern is empty.', E_USER_ERROR);
            exit;
        }

        return (bool) preg_match($this->pattern, $value);
    }
}

