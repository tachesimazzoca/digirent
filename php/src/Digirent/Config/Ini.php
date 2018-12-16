<?php

require_once 'Digirent/Config/Abstract.php';

/**
 * Digirent_Config_Ini
 *
 * /path/to/config.ini
 * <code>
 * [url]
 * domain = "example.net"
 * base   = "/"
 * [test:url]
 * domain = "test.example.net"
 * </code>
 *
 * @package Digirent_Config
 */
class Digirent_Config_Ini extends Digirent_Config_Abstract
{
    /**
     * @access private
     * @param  string
     */
    var $path;

    /**
     * @access private
     * @param  string
     */
    var $extend;

    /**
     * @access public
     * @param  string
     * @param  string
     */
    function Digirent_Config_Ini($path, $extend = null)
    {
        parent::Digirent_Config_Abstract();

        $this->path   = $path;
        $this->extend = $extend;
    }

    /**
     * @access protected
     * @return array
     */
    function & handleLoadParams()
    {
        if (($params = parse_ini_file($this->path, true)) === false) {
            $params = array();
        }

        return $params;
    }
}

