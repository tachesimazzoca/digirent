<?php

class Digirent_Controller_ActionStack
{
    var $actions = array();

    function Digirent_Controller_ActionStack()
    {
    }

    function addAction(&$action)
    {
        $this->actions[] =& $action;
    }

    function clearActions()
    {
        $this->actions = array();
    }

    function & getFirstAction()
    {
        $action = null;
        if ($this->getSize()) {
            $action =& $this->actions[0];
        }
        return $action;
    }

    function & getLastAction()
    {
        $action = null;
        if ($size = $this->getSize()) {
            $action =& $this->actions[$size - 1];
        }
        return $action;
    }

    function getSize()
    {
        return count($this->actions);
    }
}

