<?php

class Digirent_Controller_Action
{
    /**
     * @access protected 
     * @var    Digirent_Request
     */
    var $request;

    /**
     * @access protected 
     * @var    Digirent_Response
     */
    var $response;

    /**
     * @access protected
     * @var    string
     */
    var $actionName;

    /**
     * @access protected
     * @var    string
     */
    var $lastAction;

    /**
     * @access protected
     * @var    string
     */
    var $nextAction;

    /**
     * @access public
     */
    function Digirent_Controller_Action()
    {
    }

    /**
     * @access public
     * @return Digirent_Request 
     */
    function & getRequest()
    {
        return $this->request;
    }

    /**
     * @access public
     * @param  Digirent_Request
     */
    function setRequest(&$request)
    {
        $this->request =& $request;
    }

    /**
     * @access public
     * @return Digirent_Response 
     */
    function & getResponse()
    {
        return $this->response;
    }

    /**
     * @access public
     * @param  Digirent_Response
     */
    function setResponse(&$response)
    {
        $this->response =& $response;
    }

    /**
     * @access public
     * @return string
     */
    function getActionName()
    {
        return (string) $this->actionName;
    }

    /**
     * @access public
     * @param  string
     */
    function setActionName($value)
    {
        $this->actionName = (string) $value;
    }

    /**
     * @access public
     * @return string
     */
    function getLastAction()
    {
        return (string) $this->lastAction;
    }

    /**
     * @access public
     * @param  string
     */
    function setLastAction($value)
    {
        $this->lastAction = (string) $value;
    }

    /**
     * @access public
     * @return string
     */
    function getNextAction()
    {
        return (string) $this->nextAction;
    }

    /**
     * @access public
     * @param  string
     */
    function setNextAction($value)
    {
        $this->nextAction = (string) $value;
    }

    /**
     * @access public
     * @param  string
     */
    function forward($actionName)
    {
        $this->setNextAction($actionName);
    }
}

