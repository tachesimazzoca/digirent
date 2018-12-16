<?php

require_once 'Digirent/Controller/Router/Abstract.php';

class Digirent_Controller_Router_Request extends Digirent_Controller_Router_Abstract
{
    /**
     * @access public
     */
    function Digirent_Controller_Router_Request()
    {
        parent::Digirent_Controller_Router_Abstract();
    }

    /**
     * @access public 
     * @param  Digirent_Request 
     */
    function route(&$request)
    {
        $name = defined('DIGIRENT_CONTROLLER_ACTION_ACCESSOR')
              ? DIGIRENT_CONTROLLER_ACTION_ACCESSOR
              : 'action';
        $this->setActionName($request->getParam($name));
    }
}

