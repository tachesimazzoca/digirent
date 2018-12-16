<?php

class Digirent_Validator_Config_Ini
{
    function Digirent_Validator_Config_Ini()
    {
    }

    /**
     * loacConfig
     *
     * @access static public
     * @param  (string|array) /path/to/config
     * @return array 
     */
    function loadConfig(&$validator, $ini)
    {
        foreach (parse_ini_file($ini, true) as $name => $vars) {
            $depends = explode(',', @$vars['depends']);
            foreach ($depends as $depend) {
                $depend = trim($depend);
                unset($vars['depends']);
                $module = Digirent_Validator_Config::camelize($depend);
                $classname = $module . 'Rule';
                if (!class_exists($classname)) {
                    $directories = array();
                    if (defined('DIGIRENT_VALIDATOR_RULE_DIR')) {
                        $directories = explode(':', DIGIRENT_VALIDATOR_RULE_DIR);
                    }
                    $directories[] = 'Digirent/Validator/rule/';
                    foreach ($directories as $directory) {
                        $classfile = "{$directory}{$classname}.php";
                        $fp = @fopen($classfile, 'r', true);
                        if (is_resource($fp)) {
                            require_once "$classfile";
                            break;
                        }
                    }
                }
                if (!class_exists($classname)) {
                    trigger_error("'{$classname}' not found.", E_USER_ERROR);
                    exit;
                }

                $rule = new $classname();
                $properties = array();
                foreach ($vars as $key => $value) {
                    if (!preg_match('/^' . $depend . '\.(.*)$/', $key, $matches)) {
                        continue;
                    }
                    $property = $matches[1];
                    if (preg_match('/([^\.]+)\.(\d+)$/', $property, $matches)) {
                        if (!isset($properties[$matches[1]])) {
                            $properties[$matches[1]] = array();
                        }
                        if (!is_array($properties[$matches[1]])) {
                            trigger_error("'{$key}' must be an array.", E_USER_ERROR);
                            exit;
                        }
                        $properties[$matches[1]][$matches[2]] = $value;
                    } else {
                        $properties[$property] = $value;
                    }
                }
                foreach ($properties as $property => $value) {
                    $method = 'set' . Digirent_Validator_Config::camelize($property);
                    $rule->$method($value);
                }

                $validator->addRule($name, $rule);
            }
        }
    }
}

