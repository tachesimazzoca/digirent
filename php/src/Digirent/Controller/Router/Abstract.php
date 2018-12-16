<?php

class Digirent_Controller_Router_Abstract
{
    /**
     * @access private
     * @var string
     */
    var $actionName;

    /**
     * @access public
     */
    function Digirent_Controller_Router_Abstract()
    {
    }

    /**
     * @access public 
     * @return string
     */
    function getActionName()
    {
        return (string) $this->actionName;
    }

    /**
     * @access public 
     * @return
     */
    function setActionName($name)
    {
        $this->actionName = (string) $name;
    }

    /**
     * @access public 
     * @param  Digirent_Request 
     */
    function route(&$request)
    {
    }
}

