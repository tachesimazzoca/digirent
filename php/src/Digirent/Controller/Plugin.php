<?php

class Digirent_Controller_Plugin
{
    /**
     * @access protected 
     * @var    Digirent_Request
     */
    var $request;

    /**
     * @access protected 
     * @var    Digirent_Response
     */
    var $response;

    /**
     * @access protected 
     * @var    Digirent_Controller_ActionStack
     */
    var $actionStack;

    /**
     * @access protected 
     * @var    string 
     */
    var $actionName;

    /**
     * @access private 
     * @var    array 
     */
    var $triggers = array();

    /**
     * @access public
     */
    function Digirent_Controller_Plugin()
    {
    }

    function initialize()
    {
    }

    function & getActionName()
    {
        return $this->actionName;
    }

    function setActionName($value)
    {
        $this->actionName = $value;
    }

    function & getActionStack()
    {
        return $this->actionStack;
    }

    function setActionStack(&$actionStack)
    {
        $this->actionStack =& $actionStack;
    }

    function & getRequest()
    {
        return $this->request;
    }

    function setRequest(&$request)
    {
        $this->request =& $request;
    }

    function & getResponse()
    {
        return $this->response;
    }

    function setResponse(&$response)
    {
        $this->response =& $response;
    }

    /**
     * @access public 
     * @param  string hook (BEFORE_(INPUT|EXECUTE|DISPATCH|OUTPUT)|AFTER_(EXECUTE|DISPATCH|OUTPUT))
     * @param  string method 
     */
    function registerHook($hook, $method)
    {
        $hook = strtoupper($hook);
        if (preg_match('/^(BEFORE_(INPUT|DISPATCH|EXECUTE|OUTPUT)|DISPATCH|AFTER_(EXECUTE|DISPATCH|OUTPUT))$/', $hook)) {
            if (!isset($this->triggers[$hook])) {
                $this->triggers[$hook] = array();
            }
            $this->triggers[$hook][] = $method;
        }
    }

    /**
     * @access public
     * @param  string hook 
     */
    function invokeHook($hook = '')
    {
        if (!isset($this->triggers[$hook])) {
            return;
        }

        if ($this->response->isFinished()) { return; }

        foreach($this->triggers[$hook] as $value) {
            if (method_exists($this, $value)) {
                $this->$value();
            }
            if ($this->response->isFinished()) { break; }
        }
    }
}

