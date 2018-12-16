<?php

require_once 'Digirent/Validator/Rule.php';

class EmailRule extends Digirent_Validator_Rule
{
    function EmailRule()
    {
        parent::Digirent_Validator_Rule();
    }

    function check($value)
    {
        return (bool) preg_match('/^(|[a-zA-Z0-9_\.-]+@[a-zA-Z0-9\.-]+\.[a-zA-Z]{2,4})$/', $value);
    }
}

