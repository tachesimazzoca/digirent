<?php

class Digirent_Controller_Plugin_Broker
{
    /**
     * @access private 
     * @var    array 
     */
    var $plugins = array();

    /**
     * @access public
     */
    function Digirent_Controller_Plugin_Broker()
    {
    }

    /**
     * @access public
     * @param  object 
     */
    function registerPlugin(&$plugin)
    {
        if (!is_a($plugin, 'Digirent_Controller_Plugin')) {
            trigger_error("not a instance of Digirent_Controller_Plugin.", E_USER_ERROR);
        }
        $this->plugins[] =& $plugin;
    }

    /**
     * @access public
     * @param  (string|object) 
     */
    function clearPlugins()
    {
        $this->plugins = array();
    }

    /**
     * @access public 
     * @param  string 
     */
    function setActionName($value)
    {
        for ($i = 0; $i < count($this->plugins); $i++) {
            $this->plugins[$i]->setActionName($value);
        }
    }

    /**
     * @access public 
     * @param  Digirent_Controller_ActionStack
     */
    function setActionStack(&$actionStack)
    {
        for ($i = 0; $i < count($this->plugins); $i++) {
            $this->plugins[$i]->setActionStack($actionStack);
        }
    }

    /**
     * @access public
     * @param  Digirent_Request
     */
    function setRequest(&$request)
    {
        for ($i = 0; $i < count($this->plugins); $i++) {
            $this->plugins[$i]->setRequest($request);
        }
    }

    /**
     * @access public
     * @param  Digirent_Response
     */
    function setResponse(&$response)
    {
        for ($i = 0; $i < count($this->plugins); $i++) {
            $this->plugins[$i]->setResponse($response);
        }
    }

    /**
     * @access public 
     */
    function initialize()
    {
        for ($i = 0; $i < count($this->plugins); $i++) {
            $this->plugins[$i]->initialize();
        }
    }

    /**
     * @access public 
     * @param  string
     */
    function invokeHook($hook = '')
    {
        for ($i = 0; $i < count($this->plugins); $i++) {
            $this->plugins[$i]->invokeHook($hook);
        }
    }
}

