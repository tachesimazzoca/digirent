<?php

/**
 * Digirent_Request
 *
 * @package Digirent_Request
 */
class Digirent_Request
{
    /**
     * @access private
     * @var    array
     */
    var $attributes = array();

    /**
     * @access private
     * @var    array
     */
    var $params = array();

    /**
     * @access private
     * @var    array
     */
    var $cookies = array();

    /**
     * @access private
     * @var    array
     */
    var $servers = array();

    /**
     * @access private
     * @var    string 
     */
    var $body;

    /**
     * @access public
     */
    function Digirent_Request()
    {
        $this->loadParams();
        $this->loadCookies();
        $this->loadServers();
    }

    /**
     * @access private 
     */
    function _stripSlashesRecursively(&$params)
    {
        if (!is_array($params)) { return; }

        $keys = array_keys($params); 
        foreach ($keys as $key) {
            if (is_array($params[$key])) {
                Digirent_Request::_stripSlashesRecursively($params[$key]);
            } else {
                $params[$key] = stripslashes($params[$key]);
            }
        }
    }

    /**
     * @access public
     */
    function loadParams()
    {
        $this->params = array();

        if (isset($_GET) && is_array($_GET)) {
            $this->params += $_GET;
        }
        if (isset($_POST) && is_array($_POST)) {
            $this->params += $_POST;
        }
        if (get_magic_quotes_gpc()) {
            Digirent_Request::_stripSlashesRecursively($this->params);
        }
    }

    /**
     * @access public
     */
    function loadCookies()
    {
        if (isset($_COOKIE) && is_array($_COOKIE)) {
            $this->cookies = $_COOKIE;
        } else {
            $this->cookies = array();
        }

        if (get_magic_quotes_gpc()) {
            Digirent_Request::_stripSlashesRecursively($this->cookies);
        }
    }

    /**
     * @access public
     */
    function loadServers()
    {
        if (isset($_SERVER) && is_array($_SERVER)) {
            $this->servers = $_SERVER;
        } else {
            $this->servers = array();
        }
    }

    /**
     * @access public
     * @return array
     */
    function getAttributeNames()
    {
        return array_keys($this->attributes);
    }

    /**
     * @access public
     * @return array
     */
    function & getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @access public
     * @param  string
     * @return mixed
     */
    function & getAttribute($name)
    {
        $value = null;
        if (isset($this->attributes[$name])) {
            $value =& $this->attributes[$name];
        }
        return $value;
    }

    /**
     * @access public
     * @param  string
     * @param  mixed
     */
    function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * @access public
     * @param  string
     * @param  mixed
     */
    function setAttributeByRef($name, &$value)
    {
        $this->attributes[$name] =& $value;
    }

    /**
     * @access public
     */
    function removeAttributes()
    {
        return $this->attributes = array();
    }

    /**
     * @access public
     * @param  string
     */
    function removeAttribute($name)
    {
        unset($this->attributes[$name]);
    }

    /**
     * @access public 
     * @param  array
     */
    function getParamNames()
    {
        return array_keys($this->params);
    }

    /**
     * @access public 
     * @param  array
     */
    function getParams()
    {
        return $this->params;
    }

    /**
     * @access public 
     * @param  string 
     * @return mixed
     */
    function getParam($name)
    {
        $value = null;
        if (isset($this->params[$name])) {
            $value = $this->params[$name];
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
        $this->params[$name] = $value;
    }

    /**
     * @access public 
     * @return array
     */
    function getCookies()
    {
        return $this->cookies;
    }

    /**
     * @access public 
     * @param  string 
     * @return mixed
     */
    function getCookie($name)
    {
        $value = null;
        if (isset($this->cookies[$name])) {
            $value = $this->cookies[$name];
        }
        return $value;
    }

    /**
     * @access public 
     * @param  string 
     * @param  mixed
     */
    function setCookie($name, $value)
    {
        $this->cookies[$name] = $value;
    }

    /**
     * @access public
     * @return array
     */
    function getServers()
    {
        return $this->servers;
    }

    /**
     * @access public
     * @param  string
     * @return string
     */
    function getServer($name)
    {
        $value = null;
        if (isset($this->servers[$name])) {
            $value = (string) $this->servers[$name];
        }
        return $value;
    }

    /**
     * @access public
     * @param  string
     * @param  string
     */
    function setServer($name, $value)
    {
        $this->servers[$name] = (string) $value;
    }

    /**
     * @access public
     * @return string
     */
    function getBody()
    {
        if ($this->body === null) {
            $this->body = (string) implode('', file('php://input'));
        }
        return $this->body;
    }

    /**
     * @access public
     * @param  string
     */
    function setBody($value)
    {
        $this->body = (string) $value;
    }

    /**
     * @access public
     * @param  string
     * @return string
     */
    function getHeader($name)
    {
        return $this->getServer('HTTP_' . strtoupper(str_replace('-', '_', $name)));
    }

    /**
     * @access public
     * @return string
     */
    function getMethod()
    {
        return $this->getServer('REQUEST_METHOD');
    }

    /**
     * @access public
     * @return boolean
     */
    function isGet()
    {
        return ($this->getMethod() == 'GET');
    }

    /**
     * @access public
     * @return boolean
     */
    function isPost()
    {
        return ($this->getMethod() == 'POST');
    }
}

