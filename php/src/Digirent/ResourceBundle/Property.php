<?php

require_once 'Digirent/ResourceBundle/Abstract.php';

/**
 * Digirent_ResourceBundle_Ini
 *
 * This object based on .properties format.
 *
 * @package Digirent_ResourceBundle
 */
class Digirent_ResourceBundle_Property extends Digirent_ResourceBundle_Abstract
{
    /**
     * @access public
     * @param  string  /path/to/file.properties 
     */
    function Digirent_ResourceBundle_Property($properties)
    {
        parent::Digirent_ResourceBundle_Abstract();

        $this->load($properties);
    }

    /**
     * @access private 
     * @param  string
     */
    function load($properties)
    {
        $this->contents = array();

        if (!$fp = @fopen($properties, 'r')) {
            trigger_error('{$properties} could not be loaded .');
            return;
        }

        while (!feof($fp)) {

            $line = fgets($fp, 4096);
            $line = trim($line);

            preg_match('/^([^\s]+)(?:\s+)?=(?:\s+)?(.*)$/', $line, $matches);

            if (!isset($matches[1])) { continue; }

            $this->contents[$matches[1]] = $this->_ascii2native(@$matches[2]); 
        }

        @fclose($fp);
    }

    /**
     * @access private 
     * @param  string  ascii 
     * @return string  native 
     */
    function _ascii2native($str)
    {
        $es = array(
            '\b' => "\x08",
            '\t' => "\x09",
            '\n' => "\x0A",
            '\f' => "\x0C",
            '\r' => "\x0D",
            '\"' => "\x22",
            '\/' => "\x2F",
             '\\\\' => "\x5C"
        );
        $str = strtr($str, $es);

        $regex = "/\\\u([0-9a-fA-F]{4})/";
        $str = preg_replace_callback($regex, array(&$this, '_unescape'), $str);
        $str = mb_convert_encoding($str, mb_internal_encoding(), 'UTF-8');

        return $str;
    }

    /**
     * @access private 
     * @param  string unicode hex
     * @return string chars
     */
    function _unescape($matches)
    {
        $char = '';
        $cp = hexdec($matches[1]);
        switch(true) {
          case ($cp < 0x80):
            $char = chr($cp);
            break;
            
          case (0xD800 <= $cp && $cp <= 0xDFFF):
            break;

          case ($cp < 0x800):
            $char = chr($cp >> 6 & 0x1F | 0xC0) . chr($cp & 0x3F | 0x80);
            break;
            
          case ($cp < 0x10000):
            $char = chr($cp >> 12 & 0xF | 0xE0) .
            chr($cp >> 6 & 0x3F | 0x80) . chr($cp & 0x3F | 0x80);
            break;

          default:
        }

        return $char;
    }
}

