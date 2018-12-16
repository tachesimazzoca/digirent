<?php

class Digirent_Validator_Rule
{
    var $params;
    var $message;
    
    function Digirent_Validator_Rule()
    {
    }

    function getParamNames()
    {
        return array_keys($this->params);
    }

    function & getParams()
    {
        return $this->params;
    }

    function & getParam($name)
    {
        $value = null;
        if (isset($this->params[$name])) {
            $value = $this->params[$name];
        }
        return $value;
    }

    function setParam($name, $value)
    {
        $this->params[$name] = $value;
    }

    function setParams($params)
    {
        $this->params = (array) $params;
    }

    function getMessage()
    {
        return $this->message;
    }

    function setMessage($value)
    {
        $this->message = $value;
    }

    function check($value = null)
    {
    }
}
