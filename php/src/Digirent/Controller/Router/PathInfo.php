<?php

require_once 'Digirent/Controller/Router/Abstract.php';

/**
 * Digirent_Controller_Router_PathInfo
 *
 * <code>
 * </code>
 *
 * @access  public
 * @package Digirent 
 */
class Digirent_Controller_Router_PathInfo extends Digirent_Controller_Router_Abstract
{
    /**
     * @access private
     * @var    string
     */
    var $base;

    /**
     * @access private
     * @var    string
     */
    var $prefix;

    /**
     * @access public
     */
    function Digirent_Controller_Router_PathInfo()
    {
        parent::Digirent_Controller_Router_Abstract();

        if (isset($_SERVER['PHP_SELF'])) {
            $this->setBase(dirname($_SERVER['PHP_SELF']));
        }
    }

    function setBase($value)
    {
        $value = preg_replace('#/+$#', '', $value) . '/';
        $this->base = $value;
    }

    function setPrefix($value)
    {
        $this->prefix = $value;
    }

    /**
     * @access public
     * @param  Digirent_Request 
     */
    function route(&$request)
    {
        $this->setActionName('');

        $uri = '';
        if (isset($_SERVER['REQUEST_URI'])) { 
            $uri = $_SERVER['REQUEST_URI']; 

        } elseif (isset($_SERVER['HTTP_X_REWRITE_URL'])) { 
            $uri = $_SERVER['HTTP_X_REWRITE_URL']; 

        } elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0, PHP as CGI
            $uri = $_SERVER['ORIG_PATH_INFO'];

        } 

        @list($uri, $query) = explode('?', $uri);

        if (($uri = (string) $uri) === '') { return; }

        if ((string) $this->base !== '') {
            $pattern = str_replace('/', '\/', $this->base); 
            $pattern = "/^{$pattern}/";
            $uri = preg_replace($pattern, '/', $uri);
        }

        // Remove URL extension.
        $uri = preg_replace('/\.[^\.]*$/', '', $uri);

        if (!preg_match('/^\/(([0-9a-zA-Z][_0-9a-zA-Z]*\/)*)([0-9a-zA-Z][_0-9a-zA-Z]*)?$/', $uri, $matches)) {
            return;
        }

        $module = strtolower($matches[1]);
        $action = (!isset($matches[3])) ? 'index' : strtolower($matches[3]);
        $actionName = $this->prefix . implode('.', explode('/', $module)) . $action;

        $this->setActionName($actionName);
    }
}

