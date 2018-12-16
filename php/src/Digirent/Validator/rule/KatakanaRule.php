<?php

require_once 'Digirent/Validator/Rule.php';

class KatakanaRule extends Digirent_Validator_Rule
{
    function KatakanaRule()
    {
        parent::Digirent_Validator_Rule();
    }

    function check($value)
    {
        $encoding = mb_internal_encoding();
        if ($encoding != 'EUC-JP') {
            $value = mb_convert_encoding($value, 'EUC-JP', $encoding);
        }
        $regex = '(?:\xA5[\xA1-\xF6]|\xA1[\xA6\xBC\xB3\xB4]|\x20|(?:\xA1\xA1))';
        return (preg_match('/^(?:'.$regex.')*$/', $value)) ? true : false;
    }
}

