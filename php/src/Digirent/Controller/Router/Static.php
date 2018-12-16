<?php

require_once 'Digirent/Controller/Router/Abstract.php';

class Digirent_Controller_Router_Static extends Digirent_Controller_Router_Abstract
{
    /**
     * @access public
     */
    function Digirent_Controller_Router_Static()
    {
        parent::Digirent_Controller_Router_Abstract();
    }

    /**
     * @access public
     * @param  object Digirent_Request
     */
    function route(&$request)
    {
    }
}

