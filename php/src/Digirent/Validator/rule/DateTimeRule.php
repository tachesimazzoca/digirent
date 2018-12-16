<?php

require_once 'Digirent/Validator/Rule.php';

class DateTimeRule extends Digirent_Validator_Rule
{
    function DateTimeRule()
    {
        parent::Digirent_Validator_Rule();
    }

    function check($value)
    {
        if ((string) $value === '') { return true; }

        $pattern = '#^(\d{4})(?:[-/])?(\d{1,2})(?:[-/])?(\d{1,2}) ([0-1][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$#';

        if (!preg_match($pattern, $value, $matches)) {
            return false;
        }

        return (bool) checkdate((int) $matches[2], (int) $matches[3], (int) $matches[1]);
    }
}

