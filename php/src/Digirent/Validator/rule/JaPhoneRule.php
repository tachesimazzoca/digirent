<?php

require_once 'Digirent/Validator/Rule.php';

class JaPhoneRule extends Digirent_Validator_Rule
{
    var $group;
    
    function JaPhoneRule()
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

        return (bool) preg_match('/^(|\d+-?\d+-?\d+)$/', $value);
    }
}

