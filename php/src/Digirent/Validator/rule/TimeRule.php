<?php

require_once 'Digirent/Validator/Rule.php';

class TimeRule extends Digirent_Validator_Rule
{
    var $group;
    
    function TimeRule()
    {
        parent::Digirent_Validator_Rule();

        $this->group = '';
    }

    function setGroup($groups)
    {
        $this->group = (array) $groups;
    }

    function check($value)
    {
        if ($this->group) {

            $value = sprintf('%s%s',
                             $this->getParam(@$this->group[0]),
                             $this->getParam(@$this->group[1]));
            if ($value === '') { return true; }

            $value = sprintf('%02d%02d',
                             $this->getParam(@$this->group[0]),
                             $this->getParam(@$this->group[1]));
        } else {
            if ($value === '') { return true; }
        }

        return (bool) preg_match('/^(0?[0-9]|1[0-9]|2[0-3]):?(0?[0-9]|[1-5][0-9])$/', $value);
    }
}

