<?php

require_once 'Digirent/Controller/Dispatcher/Abstract.php';
require_once 'Digirent/Controller/Action.php';

/**
 * Digirent_Controller_Dispatcher
 *
 * <code>
 * </code>
 *
 * @access  public
 * @package Digirent 
 */
class Digirent_Controller_Dispatcher extends Digirent_Controller_Dispatcher_Abstract
{
    var $directory;
    var $defaultActionClassPath;
    var $defaultActionClassName;

    /**
     * @access public
     */
    function Digirent_Controller_Dispatcher()
    {
        parent::Digirent_Controller_Dispatcher_Abstract();

        $this->actionMethods = array('preExecute', 'execute', 'postExecute');

        foreach (array('DIGIRENT_CONTROLLER_DISPATCHER_DIRECTORY', 'DIGIRENT_CONTROLLER_DISPATCHER_MODULE_DIR') as $key) {
            // "DIGIRENT_CONTROLLER_DISPATCHER_MODULE_DIR" has been deprecated.
            if (defined($key)) {
                $this->setDirectory(constant($key));
                break;
            }
        }
    }

    /**
     * @access public
     * @param  Digirent_Request
     */
    function setDirectory($value)
    {
        $this->directory = (string) $value;
    }

    /**
     * @access public
     * @param  string
     */
    function setDefaultActionClassPath($value)
    {
        $this->defaultActionClassPath = (string) $value;
    }

    /**
     * @access public
     * @param  string
     */
    function setDefaultActionClassName($value)
    {
        $this->defaultActionClassName = (string) $value;
    }

    /**
     * @access public
     * @param  string
     */
    function setDefaultAction($classname, $path = null)
    {
        $this->setDefaultActionClassName($classname);
        if ((string) $path !== '') {
            $this->setDefaultActionClassPath($path);
        }
    }

    /**
     * @access public
     * @param  string
     * @return Digirent_Controller_Action
     */
    function & getAction($actionName)
    {
        $action = null;

        $classname = $this->formatActionClassName($actionName);
        if (class_exists($classname)) {
            $action = new $classname();
            return $action;
        }

        $classfile = $this->formatActionClassPath($actionName);

        if (!is_readable($classfile)) {
            if (($classname = (string) $this->defaultActionClassName) === '') {
                $action = new Digirent_Controller_Action(); 
                return $action;
            }
            if (($classfile = (string) $this->defaultActionClassPath) !== '') {
                if (substr($classfile, 0) != DIRECTORY_SEPARATOR) {
                    $classfile = $this->directory . $classfile;
                }
            }
        }

        if ($classfile !== '') {
            require_once $classfile;
        }

        $action = new $classname();

        return $action;
    }

    /**
     * @access public
     * @param  string
     * @return string
     */
    function formatActionClassPath($name)
    {
        $directory = dirname($this->directory . sprintf("%s/", str_replace('.', '/', $name))) . '/';
        return $directory . sprintf("%sAction.php", $this->_camelize($name, ''));
    }

    /**
     * @access public
     * @param  string
     * @return string
     */
    function formatActionClassName($name)
    {
        return $this->_camelize($name, '') . 'Action';
    }

    /**
     * @access private
     */
    function _camelize($string, $delimiter = '')
    {
        $words = array();
        foreach (explode('.', $string) as $word) {
            $word = str_replace('_', ' ', strtolower($word));
            $word = str_replace(' ', '', ucwords($word));
            $words[] = $word;
        }
        return implode($delimiter, $words);
    }
}

