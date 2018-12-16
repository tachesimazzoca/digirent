<?php

/**
 * Digirent_Renderer_Abstract
 *
 * @package Digirent_Renderer
 */
class Digirent_Renderer_Abstract
{
    /**
     * @access protected
     * @var    string
     */
    var $directory = '';

    /**
     * @access protected
     * @var    string
     */
    var $template = '';

    /**
     * @access protected
     * @var    string
     */
    var $extension = '';

    /**
     * @access protected
     * @var    string
     */
    var $result = null;

    /**
     * @access public
     */
    function Digirent_Renderer_Abstract()
    {
    }

    /**
     * @access public
     */
    function execute()
    {
    }

    /**
     * @access public 
     * @return string
     */
    function & fetchResult()
    {
        if (is_null($this->result)) {
            $this->execute();
        }
        return $this->result;
    }

    /**
     * @access public
     * @return string 
     */
    function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @access public
     * @param  string 
     */
    function setDirectory($value)
    {
        if (substr($value, -1) !== DIRECTORY_SEPARATOR) {
            $value .= DIRECTORY_SEPARATOR;
        }
        $this->directory = $value;
    }

    /**
     * @access public 
     * @return string 
     */
    function getTemplate()
    {
        return $this->template;
    }

    /**
     * @access public
     * @param  string 
     */
    function setTemplate($value)
    {
        $this->template = $value;
    }

    /**
     * @access public
     * @return string 
     */
    function getExtension()
    {
        return $this->extension;
    }

    /**
     * @access public
     * @param  string 
     */
    function setExtension($value)
    {
        preg_replace('/^\.+/', '', $value);
        $this->extension = $value;
    }

    /**
     * @access public
     * @return string 
     */
    function getTemplatePath()
    {
        $path = $this->directory . $this->template;

        if (!preg_match('/\.[^\.]+$/', $path)) {
            $path .= '.' . $this->extension;
        }
        if (substr($path, 0, 1) !== DIRECTORY_SEPARATOR) {
            if (defined('DIGIRENT_RENDERER_TEMPLATE_DIR')) {
                $path = DIGIRENT_RENDERER_TEMPLATE_DIR . $path;
            }
        }
        return $path;
    }

    /**
     * @access public
     * @param  string 
     */
    function removeParam($name)
    {
    }

    /**
     * @access public
     */
    function removeParams()
    {
    }

    /**
     * @access public
     * @param  string 
     * @return mixed
     */
    function & getParam($name)
    {
    }

    /**
     * @access public
     * @return array
     */
    function & getParams()
    {
    }

    /**
     * @access public
     * @param  string 
     * @param  mixed 
     */
    function setParam($name, $value)
    {
    }

    /**
     * @access public
     * @param  array 
     */
    function setParams($values)
    {
        foreach ($values as $key => $value) {
            $this->setParam($key, $value);
        }
    }

    /**
     * @access public
     * @param  string 
     * @param  array 
     */
    function setParamByRef($name, &$value)
    {
    }
}

