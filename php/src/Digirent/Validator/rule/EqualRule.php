<?php

require_once 'Digirent/Validator/Rule.php';

class EqualRule extends Digirent_Validator_Rule
{
    var $source;

    function EqualRule()
    {
        parent::Digirent_Validator_Rule();

        $this->source = '';
    }

    function setSource($value)
    {
        $this->source = $value;
    }

    function check($value)
    {
        return (bool) ($value == $this->getParam($this->source));
    }
}

