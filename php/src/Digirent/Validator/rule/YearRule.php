<?php

require_once 'Digirent/Validator/Rule.php';

class YearRule extends Digirent_Validator_Rule
{
    var $from;
    var $to;
    
    function YearRule()
    {
        parent::Digirent_Validator_Rule();
    }

    function setFrom($value)
    {
        $this->from = (int) $value;
    }

    function setTo($value)
    {
        $this->to = (int) $value;
    }

    function check($value)
    {
        if (!preg_match('/^\d+$/', $value)) {
            return false;
        }

        $year = date('Y');
        $from = ($this->from !== null) ? (int) $this->from : $year;
        $to   = ($this->to   !== null) ? (int) $this->to   : $year;

        return ($from <= $value && $to >= $value);
    }
}

