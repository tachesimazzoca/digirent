<?php

class Digirent_Validator_Config
{
    function loadConfig(&$validator, $config)
    {
        // TODO: (xml|yaml) support
        $supports = array('ini');

        $extensions = array();
        $extension = (string) pathinfo($config, PATHINFO_EXTENSION);

        if ((string) $extension === '') {
            $extensions = $supports;
        } else {
            $extensions[] = $extension;
            $config = substr($config, 0, -1 * (strlen($extension) + 1));
        }

        $path = null;

        foreach ($extensions as $extension) {
            $path = $config . '.' . $extension;
            if (is_readable($path)) {
                break;
            }
            $path = null;
        }

        if ($path === null) {
            trigger_error('The configuration file is not readable. ' . $path, E_USER_ERROR);
        }

        $subclass = ucfirst(strtolower(pathinfo($path, PATHINFO_EXTENSION)));

        require_once 'Digirent/Validator/Config/' . $subclass . '.php';
        call_user_func(array('Digirent_Validator_Config_' . $subclass, 'loadConfig'), $validator, $path);
    }

    function camelize($string, $delimiter = '')
    {
        $words = array();
        foreach (explode('.', $string) as $word) {
            if (ereg('_', $word)) {
                $word = str_replace('_', ' ', strtolower($word));
                $word = str_replace(' ', '', ucwords($word));
            }
            $words[] = ucfirst($word);
        }
        return implode($delimiter, $words);
    }
}
