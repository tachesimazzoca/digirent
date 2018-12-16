<?php

require_once 'Digirent/Db/Pager.php';

/**
 * Digirent_Db_Adapter_Abstract
 *
 * @package Digirent_Db
 */
class Digirent_Db_Adapter_Abstract
{
    /**
     * @access protected 
     * @var    array 
     */
    var $messages;

    /**
     * @access protected 
     * @var    string
     */
    var $encoding;

    /**
     * @access private 
     * @var    callback 
     */
    var $errorCallback;

    /**
     * @access public 
     */
    function Digirent_Db_Adapter_Abstract()
    {
    }

    /**
     * @access public 
     * @param  string 
     */
    function getMessage($glue = PHP_EOL)
    {
        return implode($glue, $this->messages);
    }

    /**
     * @access public 
     * @param  array 
     */
    function & getMessages()
    {
        return $this->messages;
    }

    /**
     * @access public 
     * @return string
     */
    function getEncoding()
    {
        if ($this->encoding === null) {
            $this->setEncoding(mb_internal_encoding());
        }
        return $this->encoding;
    }

    /**
     * @access public 
     * @param  string
     */
    function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    /**
     * @access public 
     */
    function connect()
    {
    }

    /**
     * @access public 
     */
    function disconnect()
    {
    }

    /**
     * @access public 
     * @return integer 
     */
    function affectedRows()
    {
    }

    /**
     * @access public 
     * @param  string 
     * @param  array
     * @return boolean
     */
    function insert($table, $bind)
    {
    }

    /**
     * @access public 
     * @param  string 
     * @param  array
     * @param  string 
     * @return boolean
     */
    function update($table, $bind, $where = '')
    {
    }

    /**
     * @access public 
     * @param  string 
     * @param  array
     * @param  array
     * @return object 
     */
    function & query($sql, $bind = array(), $limit = array())
    {
        $result = null;
        return $result;
    }

    /**
     * @access public 
     * @param  array 
     */
    function encode(&$values)
    {
        $external = (string) $this->getEncoding();
        $internal = (string) mb_internal_encoding();
        if ($internal === $external) { return; }

        foreach (array_keys($values) as $key) {
            if (is_string($values[$key])) {
                $values[$key] = mb_convert_encoding($values[$key], $external, $internal);
            }
        }
    }

    /**
     * @access private 
     * @param  string 
     * @return string 
     */
    function escape($value)
    {
    }

    /**
     * @access private 
     * @param  string 
     * @return string 
     */
    function quoteIdentifier($value)
    {
    }

    /**
     * @access private 
     * @param  string 
     * @return string 
     */
    function quoteSmart($value)
    {
    }

    /**
     * @access private 
     * @return object 
     */
    function & pager()
    {
        $pager = new Digirent_Db_Pager();
        $pager->setAdapter($this);

        return $pager;
    }

    /**
     * @access protected
     * @param  callback
     */
    function onError($callback = null)
    {
        if ($callback !== null) {
            $this->errorCallback =& $callback;
        } else {
            if ($this->errorCallback !== null) {
                call_user_func($this->errorCallback, $this->getMessage());
            }
        }
    }
}

