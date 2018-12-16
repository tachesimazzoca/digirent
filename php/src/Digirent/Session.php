<?php

/**
 * Digirent_Session
 *
 * <code>
 * $session =& Digirent_Session::getInstance();
 * $session->setParam('foo', 'bar');
 *
 * echo $session->getParam('foo'); // "bar"
 *
 * $session->removeParam('foo');
 * var_dump($session->getParam('foo')); // is NULL 
 *
 * $session->destroy();
 * var_dump($session->getParams()); // array()
 * </code>
 *
 * @package Digirent_Session
 */
class Digirent_Session
{
    /**
     * @access private
     */
    function Digirent_Session()
    {
        session_start();
    }

    /**
     * Return the singleton instance of the Digirent_Session object.
     *
     * @access public
     * @return object
     */
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
     * @return array
     */
    function getParamNames()
    {
        return array_keys($_SESSION);
    }

    /**
     * @access public
     * @return array
     */
    function & getParams()
    {
        return $_SESSION;
    }

    /**
     * @access public
     * @param  string
     * @return mixed
     */
    function & getParam($name)
    {
        $value = null;
        if (isset($_SESSION[$name])) {
            $value = $_SESSION[$name];
        }
        return $value;
    }

    /**
     * @access public
     * @param  string
     * @param  mixed
     */
    function setParam($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * @access public
     * @param  string
     */
    function removeParam($name)
    {
        unset($_SESSION[$name]);
    }

    /**
     * Empty the $_SESSION superglobal and destroy the session.
     *
     * @access public
     */
    function destroy()
    {
        $_SESSION = array();
        session_destroy();
    }
}

