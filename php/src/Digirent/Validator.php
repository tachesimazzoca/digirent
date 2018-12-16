<?php

/**
 * @package digirent
 */
class Digirent_Validator
{
    var $rules = array();
    var $messages = array();

    /**
     * @access public
     */
    function Digirent_Validator()
    {
        $this->rules    = array();
        $this->messages = array();
    }

    /**
     * @access public static
     * @param  string /path/to/config
     * @return object Digirent_Validator
     */
    function & factory($config = null)
    {
        $validator = new Digirent_Validator();

        if ($config !== null) {
            require_once 'Digirent/Validator/Config.php';
            Digirent_Validator_Config::loadConfig($validator, $config);
        }

        return $validator;
    }

    /**
     * @access public
     * @param  string 
     * @param  array  [Digirent_Validator_Rule_Abstract]
     */
    function & getRules($name)
    {
        $rules = null;
        if (isset($this->rules[$name])) {
            $rules =& $this->rules[$name];
        }
        return $rules;
    }

    /**
     * @access public
     * @param  string 
     * @param  object Digirent_Validator_Rule_Abstract
     */
    function addRule($name, &$rule)
    {
        if (!isset($this->rules[$name])) {
            $this->rules[$name] = array();
        }
        $this->rules[$name][] = $rule;
    }

    /**
     * @access public
     */
    function removeRules()
    {
        $this->rules = array();
    }

    /**
     * @access public
     * @param  string 
     */
    function removeRule($name)
    {
        unset($this->rules[$name]);
    }

    /**
     * @access public
     * @return array
     */
    function & getMessages()
    {
        $values = array();
        foreach ($this->messages as $key => $value) {
            $values[] = $value;
        }
        return $values;
    }

    /**
     * @access public
     * @param  string 
     * @return string 
     */
    function getMessage($name)
    {
        $value = '';
        if (isset($this->messages[$name])) {
            $value = $this->messages[$name];
        }
        return $value;
    }

    /**
     * @access public
     * @param  string
     * @param  string 
     */
    function setMessage($name, $value)
    {
        $this->messages[$name] = $value;
    }

    /**
     * @access public
     * @param  string 
     * @return boolean 
     */
    function hasMessage($name)
    {
        return (bool) isset($this->messages[$name]);
    }

    /**
     * @access public
     * @return boolean
     */
    function isValid()
    {
        return $this->messages ? false : true;
    }

    /**
     * @access public
     */
    function reset()
    {
        $this->messages = array();
    }

    /**
     * @access public
     * @param  array
     * @return boolean
     */
    function execute($params)
    {
        $this->reset();

        foreach (array_keys($this->rules) as $name) {
            for ($i = 0; $i < count($this->rules[$name]); $i++) {
                $this->rules[$name][$i]->setParams($params);
                $value = isset($params[$name]) ? $params[$name] : null;
                if ($this->rules[$name][$i]->check($value)) {
                    continue;
                }
                if (!$this->hasMessage($name)) {
                    $this->setMessage($name, $this->rules[$name][$i]->getMessage());
                }
                break;
            }
        }

        return $this->isValid();
    }
}

