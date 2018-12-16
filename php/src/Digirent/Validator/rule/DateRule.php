<?php

require_once 'Digirent/Validator/Rule.php';

class DateRule extends Digirent_Validator_Rule
{
    var $group;
    
    function DateRule()
    {
        parent::Digirent_Validator_Rule();

        $this->group = array();
    }

    function setGroup($value)
    {
        $this->group = (array) $value;
    }

    function check($value)
    {
        if ($this->group) {
            $values = array();
            foreach ($this->group as $key) {
                $values[] = (string) $this->getParam($key);
            }
            $value = (implode('', $values) == '') ? '' : implode('-', $values);
        }

        if ($value == '') { return true; }

        if (!preg_match('/^(\d{4})(?:[\-\/])?(\d{1,2})(?:[\-\/])?(\d{1,2})$/', $value, $matches)) {
            return false;
        }

        return (bool) checkdate((int) $matches[2], (int) $matches[3], (int) $matches[1]);
    }
}

