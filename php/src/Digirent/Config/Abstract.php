<?php

/**
 * Digirent_Config_Abstract
 *
 * @package Digirent_Config
 */
class Digirent_Config_Abstract
{
    /**
     * @access private 
     * @param  array 
     */
    var $params;

    /**
     * @access public
     */
    function Digirent_Config_Abstract()
    {
    }

    /**
     * @access protected
     * return  array
     */
    function & handleLoadParams()
    {
        $params = array();
        return $params;
    }

    /**
     * @access public
     */
    function load()
    {
        $this->params = array();

        if (!$params =& $this->handleLoadParams()) {
            return;
        }

        $extends = array();

        foreach ($params as $section => $vars) {

            $section = str_replace(' ', '', $section);

            if (!preg_match('/^([^\:]+):?([^\:]+)?$/', $section, $matches)) {
                continue;
            }

            if (isset($matches[2])) {
                if ((string) $this->extend === $matches[1]) {
                    $extends[$matches[2]] = $vars;
                }
            } else {
                $this->params[$matches[1]] = $vars;
            }
        }

        foreach ($extends as $section => $vars) {
            foreach ($vars as $key => $value) {
                $this->params[$section][$key] = $value;
            }
        }
    }

    /**
     * @access public
     * @return array
     */
    function & getParams()
    {
        if ($this->params === null) {
            $this->load();
        }

        return $this->params;
    }

    /**
     * @access public
     * @param  string 
     * @param  string 
     * @return mixed   a value of the key or an array of the section.
     */
    function getParam($section, $key = null)
    {
        if ($this->params === null) {
            $this->load();
        }

        if ($key === null) {
            $value = (isset($this->params[$section])) ? $this->params[$section] : null;
        } else {
            $value = (isset($this->params[$section][$key])) ? $this->params[$section][$key] : null;
        }

        return $value;
    }
}

