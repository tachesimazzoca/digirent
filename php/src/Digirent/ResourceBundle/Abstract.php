<?php

/**
 * Digirent_ResourceBundle_Abstract
 *
 * @package Digirent_ResourceBundle
 */
class Digirent_ResourceBundle_Abstract
{
    /**
     * @access public 
     * @var    array
     */
    var $contents = array();

    /**
     * @access public 
     */
    function Digirent_ResourceBundle_Abstract()
    {
    }

    /**
     * @access protected
     * @return object 
     */
    function & handleGetObject($key)
    {
        $content = null;
        if (isset($this->contents[$key])) {
            $content = $this->contents[$key];
        }
        return $content;
    }

    /**
     * @access public
     * @return mixed 
     */
    function & getObject($key)
    {
        $object =& $this->handleGetObject($key);

        return $object;
    }

    /**
     * @access public 
     * @return string 
     */
    function getString($key)
    {
        $object =& $this->getObject($key);

        return (string) $object;
    }

    /**
     * @access public
     * @return array
     */
    function & getContents()
    {
        return $this->contents;
    }
}

