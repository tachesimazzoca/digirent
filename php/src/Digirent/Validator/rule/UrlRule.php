<?php

require_once 'Digirent/Validator/Rule.php';

class UrlRule extends Digirent_Validator_Rule
{
    function UrlRule()
    {
        parent::Digirent_Validator_Rule();
    }

    function check($value)
    {
        $protocol    = '(https?:\/\/)';
        $domain      = '([-a-zA-Z0-9]+(\.[-a-zA-Z0-9]+)+)';
        $IPv4address = '(\d+\.\d+\.\d+\.\d+)';
        $address     = "({$domain}|{$IPv4address}(:\\d+)?)";
        $path        = '(\/.+)';
        $regex       = $protocol.'?'.$address.'?'.$path.'*\/?';

        return ( $value == '' || preg_match('/^'.$regex.'$/', $value) ) ? true : false;
    }
}

