<?php

require_once 'Digirent/Validator/Rule.php';

class MaxlengthRule extends Digirent_Validator_Rule
{
    var $length;

    function MaxlengthRule()
    {
        parent::Digirent_Validator_Rule();
    }

    function setLength($value)
    {
        $this->length = $value;
    }

    function check($value)
    {
        return (bool) (mb_strlen($value) <= $this->length);
    }
}

