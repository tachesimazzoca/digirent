<?php

/**
 * Digirent_Db_Dxo_Camelize 
 *
 * @package Digirent_Db
 */
class Digirent_Db_Dxo_Camelize
{
    /**
     * @access public
     * @param  array 
     * @param  obejct
     * @return object
     */
    function convert(&$params, &$dto)
    {
        $vars = (array) get_object_vars($dto);
        $alias = array();
        foreach ($vars as $var => $value) {
            preg_match('/^(.+)_COLUMN$/i', $var, $matches);
            if (isset($matches[1])) {
                $alias[$matches[1]] = $value;
            }
        }

        foreach ($params as $key => $value) {
            $method = 'set';
            if ($property = array_search($key, $alias)) {
                $method .= ucfirst($property);
            } else {
                $method .= $this->_camelize($key);
            }
            if (method_exists($dto, $method)) {
                $dto->$method($value);
            }
        }
    }

    /**
     * @access private 
     * @param  string 
     * @return string 
     */
    function _camelize($str)
    {
        $str = str_replace('_', ' ', strtolower($str));
        $str = str_replace(' ', '', ucwords($str));
        $str = preg_replace('/[^_0-9a-zA-Z]/', '', $str);

        return $str;
    }
}

