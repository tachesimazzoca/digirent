<?php

require_once 'Digirent/ResourceBundle/Abstract.php';

/**
 * Digirent_ResourceBundle_Ini
 *
 * This object based on .ini format.
 *
 * @package Digirent_ResourceBundle
 */
class Digirent_ResourceBundle_Ini extends Digirent_ResourceBundle_Abstract
{
    /**
     * @access public
     * @param  string /path/to/file.ini
     */
    function Digirent_ResourceBundle_Ini($ini)
    {
        parent::Digirent_ResourceBundle_Abstract();

        $this->contents = parse_ini_file($ini, true);
    }
}

