<?php

require_once 'Digirent/Renderer/Abstract.php';

/**
 * Digirent_Renderer_PHP
 *
 * The renderer based on the PHP core.
 *
 * @package Digirent_Renderer
 */
class Digirent_Renderer_PHP extends Digirent_Renderer_Abstract
{
    /**
     * @access private 
     * @var    array
     */
    var $params = array();

    /**
     * @access public
     */
    function Digirent_Renderer_PHP()
    {
        parent::Digirent_Renderer_Abstract();

        $this->extension = 'php';
    }

    /**
     * @access public
     */
    function execute()
    {
        // This variable "$params" is deprecated.
        // Use the getParam() method insted.
        $params =& $this->getParams();
        ob_start();
        require($this->getTemplatePath());
        $this->result = ob_get_contents();
        ob_end_clean();
    }

    /**
     * @access public
     * @param  string
     * @return mixed 
     */
    function & getParam($name)
    {
        $value = null;
        if (isset($this->params[$name])) {
            $value =& $this->params[$name];
        }
        return $value;
    }

    /**
     * @access public
     */
    function & getParams()
    {
        return $this->params;
    }

    /**
     * @access public
     * @param  string
     * @param  mixed 
     */
    function setParam($name, $value)
    {
        $this->params[$name] = $value;
    }

    /**
     * @access public
     * @param  string
     * @param  mixed 
     */
    function setParamByRef($name, &$value)
    {
        $this->params[$name] =& $value;
    }

    /**
     * @access public
     * @param  string
     */
    function removeParam($name)
    {
        unset($this->params[$name]);
    }

    /**
     * @access public
     */
    function removeParams()
    {
        $this->params = array();
    }
}

