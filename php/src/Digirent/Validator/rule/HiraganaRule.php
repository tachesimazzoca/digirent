<?php

require_once 'Digirent/Validator/Rule.php';

class HiraganaRule extends Digirent_Validator_Rule
{
    function HiraganaRule()
    {
        parent::Digirent_Validator_Rule();
    }

    function check($value)
    {
        $encoding = mb_internal_encoding();
        if ($encoding != 'EUC-JP') {
            $value = mb_convert_encoding($value, 'EUC-JP', $encoding);
        }

        // 全角ひらがな(拡張) [ぁ-ん゛゜ゝゞー]
        $regex = '(?:\xA4[\xA1-\xF3]|\xA1[\xAB\xAC\xB5\xB6\xBC]|\x20|(?:\xA1\xA1))';
        return (preg_match('/^(?:'.$regex.')*$/', $value)) ? true : false;
    }
}

