<?php

/**
 * Digirent_Controller_Dispatcher_Abstract
 *
 * <code>
 * </code>
 *
 * @access  public
 * @package Digirent 
 */
class Digirent_Controller_Dispatcher_Abstract
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
     * @var    Digirent_Controller_Plugin_Broker
     */
    var $plugin;

    /**
     * @access protected
     * @var    Digirent_Controller_ActionStack
     */
    var $actionStack;

    /**
     * @access protected
     * @var    array
     */
    var $actionMethods = array();

    /**
     * @access public
     */
    function Digirent_Controller_Dispatcher_Abstract()
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
     * @param  Digirent_Controller_Plugin_Broker
     */
    function setPlugin(&$plugin)
    {
        $this->plugin =& $plugin;
    }

    /**
     * @access public
     * @param  Digirent_Controller_ActionStack
     */
    function setActionStack(&$actionStack)
    {
        $this->actionStack =& $actionStack;
    }

    /**
     * @access public
     * @return array
     */
    function getActionMethods()
    {
        return (array) $this->actionMethods;
    }

    /**
     * @access public
     * @param  array
     */
    function setActionMethods($values)
    {
        $this->actionMethods = (array) $values;
    }

    /**
     * @access public
     * @param  string
     * @return Digirent_Controller_Action
     */
    function & getAction($actionName)
    {
        $action = null;
        return $action;
    }

    /**
     * @access public
     * @param  string
     */
    function dispatch($actionName)
    {
        $lastAction = '';
        $nextAction = $actionName;

        $n = 0;

        while ((string) $nextAction !== '') {

            $n++;
            if ($n > 99) {
                trigger_error("Maximum number of action chains.", E_USER_ERROR);
            }

            if ((string) $lastAction === (string) $nextAction) {
                trigger_error("Can NOT foward to same action.", E_USER_ERROR);
            }

            $action =& $this->getAction($nextAction); 
            if ($action === null) {
                return;
            }

            $this->actionStack->addAction($action);

            $action->setRequest($this->request);
            $action->setResponse($this->response);

            $action->setActionName($nextAction);
            $action->setLastAction($lastAction);
            $action->setNextAction('');

            $this->plugin->invokeHook('BEFORE_EXECUTE');
            if ($this->response->isFinished()) {
                return;
            }

            $lastAction = $nextAction;
            $nextAction = '';

            foreach ($this->getActionMethods() as $method) {
                if (!method_exists($action, $method)) {
                    continue;
                }
                $action->$method();
                if ($this->response->isFinished()) {
                    return;
                }
                $nextAction = $action->getNextAction();
                if ((string) $nextAction !== '') {
                    break;
                }
            }

            $this->plugin->invokeHook('AFTER_EXECUTE');
            if ($this->response->isFinished()) {
                return;
            }
        }
    }
}

