<?php

require_once 'Digirent/Controller/ActionStack.php';
require_once 'Digirent/Controller/Plugin/Broker.php';

/**
 * Digirent_Controller_Front
 *
 * <code>
 * </code>
 *
 * @access  public
 * @package Digirent 
 */
class Digirent_Controller_Front
{
    /**
     * @access private
     * @var    Digirent_Request
     */
    var $request;

    /**
     * @access private
     * @var    Digirent_Response
     */
    var $response;

    /**
     * @access private
     * @var    Digirent_Controller_Router 
     */
    var $router;

    /**
     * @access private 
     * @var    Digirent_Controller_Dispathcer
     */
    var $dispatcher;

    /**
     * @access private
     * @var    Digirent_Controller_Plugin_Broker
     */
    var $plugin;

    /**
     * @access private 
     * @var    Digirent_Controller_ActionStack
     */
    var $actionStack;

    /**
     * @access public
     */
    function Digirent_Controller_Front()
    {
        $this->actionStack = new Digirent_Controller_ActionStack();
        $this->plugin = new Digirent_Controller_Plugin_Broker();
    }

    function & getInstance()
    {
        static $instance;
        if (is_null($instance)) {
            $classname = __CLASS__;
            $instance = new $classname();
        }
        return $instance;
    }

    /**
     * @access public 
     * @return Digirent_Request
     */
    function & getRequest()
    {
        if (is_null($this->request)) {
            require_once 'Digirent/Request.php';
            $this->request = new Digirent_Request();
        }
        return $this->request;
    }

    /**
     * @access protected 
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
        if (is_null($this->response)) {
            require_once 'Digirent/Response.php';
            $this->response = new Digirent_Response();
        }
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
     * @return Digirent_Controller_Router
     */
    function & getRouter()
    {
        if (is_null($this->router)) {
            require_once 'Digirent/Controller/Router/PathInfo.php';
            $this->router = new Digirent_Controller_Router_PathInfo();
        }
        return $this->router;
    }

    /**
     * @access public 
     * @param  Digirent_Controller_Router
     */
    function setRouter(&$router)
    {
        $this->router =& $router;
    }

    /**
     * @access public 
     * @return Digirent_Controller_Dispatcher
     */
    function & getDispatcher()
    {
        if (is_null($this->dispatcher)) {
            require_once 'Digirent/Controller/Dispatcher.php';
            $this->dispatcher = new Digirent_Controller_Dispatcher();
        }
        return $this->dispatcher;
    }

    /**
     * @access public 
     * @param  Digirent_Controller_Dispatcher
     */
    function setDispatcher(&$dispatcher)
    {
        $this->dispatcher =& $dispatcher;
    }

    /**
     * @access public
     * @param  string actionName
     */
    function dispatch($actionName = null)
    {
        $router =& $this->getRouter();
        $dispatcher =& $this->getDispatcher();
        $request =& $this->getRequest();
        $response =& $this->getResponse();

        if ((string) $actionName === '') {
            $router->route($request);
            $actionName = $router->getActionName();
        }

        if ((string) $actionName === '') {
            return;
        }

        $this->plugin->setActionName($actionName);
        $this->plugin->setActionStack($this->actionStack);
        $this->plugin->setRequest($request);
        $this->plugin->setResponse($response);
        $this->plugin->initialize();

        $this->plugin->invokeHook('BEFORE_INPUT');
        if ($response->isFinished()) { return; }

        $this->plugin->invokeHook('BEFORE_DISPATCH');
        if ($response->isFinished()) { return; }

        // dispatch
        $dispatcher->setActionStack($this->actionStack);
        $dispatcher->setRequest($request);
        $dispatcher->setResponse($response);
        $dispatcher->setPlugin($this->plugin);
        $dispatcher->dispatch($actionName);
        if ($response->isFinished()) { return; }

        $this->plugin->invokeHook('AFTER_DISPATCH');
        if ($response->isFinished()) { return; }

        $this->plugin->invokeHook('BEFORE_OUTPUT');

        $response->sendResponse();

        $this->plugin->invokeHook('AFTER_OUTPUT');
    }

    /**
     * @access public
     * @param  Digirent_Controller_Plugin 
     */
    function registerPlugin(&$plugin)
    {
        $this->plugin->registerPlugin($plugin);
    }

    /**
     * @access public
     */
    function clearPlugins()
    {
        $this->plugin->clearPlugins();
    }
}

