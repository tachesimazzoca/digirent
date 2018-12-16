<?php

require_once 'Digirent/Validator/Rule.php';

class JaZipRule extends Digirent_Validator_Rule
{
    var $group;
    
    function JaZipRule()
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

        return (bool) preg_match('/^(|\d{3}-?\d{4})$/', $value);
    }
}

