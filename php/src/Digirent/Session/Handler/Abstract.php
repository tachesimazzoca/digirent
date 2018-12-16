<?php

/**
 * Digirent_Session_Handler_Abstract
 *
 * @package Digirent_Session_Handler
 */
class Digirent_Session_Handler_Abstract
{
    /**
     * @access protected
     */
    function Digirent_Session_Handler_Abstract()
    {
    }

    /**
     * @access private 
     */
    function sessName($name = '')
    {
        static $sessName;
        if ($name) {
            $sessName = $name;
        }
        return $sessName;
    }

    /**
     * @access public 
     * @param  string
     * @param  string
     * @return boolean 
     */
    function open($path, $name)
    {
        $this->sessName($name);
        return true;
    }

    /**
     * @access public 
     * @return boolean 
     */
    function close()
    {
        return true;
    }

    /**
     * @access public 
     * @param  string
     * @return string
     */
    function read($sid)
    {
        $sess_data = null;
        return $sess_data;
    }

    /**
     * @access public
     * @param  string
     * @param  string
     * @return boolean 
     */
    function write($sid, $data)
    {
        return false;
    }

    /**
     * @access public
     * @param  string
     * @return boolean 
     */
    function destroy($sid)
    {
        return false;
    }

    /**
     * @access public
     * @param  integer 
     * @return boolean 
     */
    function gc($maxlifetime)
    {
        return false;
    }
}

