<?php

require_once 'Digirent/Storage/Abstract.php';

/**
 * Digirent_Storage_Memcache
 *
 * SYNOPSIS:
 * <code>
 * $options = array(
 *     'server' => 'localhost',
 *     'port' => 11211,
 *     'timeout' => 5,
 * );
 * $storage =& Digirent_Storage::factory('Memcache', $options);
 * </code>
 *
 * @package Digirent_Storage
 */
class Digirent_Storage_Memcache extends Digirent_Storage_Abstract
{
    /**
     * @access private
     * @var    string
     */
    var $server;

    /**
     * @access private
     * @var    integer
     */
    var $port;

    /**
     * @access private
     * @var    integer
     */
    var $timeout;

    /**
     * @access public
     * @param  array
     */
    function Digirent_Storage_Memcache($options = array())
    {
        parent::Digirent_Storage_Abstract();

        $this->server = isset($options['server']) ? (string) $options['server'] : 'localhost';
        $this->port = isset($options['port']) ? (int) $options['port'] : 11211;
        $this->timeout = isset($options['timeout']) ? (int) $options['timeout'] : 5;
    }

    /**
     * @access public
     * @return string
     */
    function getServer()
    {
        return (string) $this->server;
    }

    /**
     * @access public
     * @param  string
     */
    function setServer($value)
    {
        $this->server = (string) $value;
    }

    /**
     * @access public
     * @return integer
     */
    function getPort()
    {
        return $this->port;
    }

    /**
     * @access public
     * @param  integer
     */
    function setPort($value)
    {
        $this->port = (int) $value;
    }

    /**
     * @access public
     * @return integer
     */
    function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @access public
     * @param  integer
     */
    function setTimeout($value)
    {
        $this->timeout = (int) $value;
    }

    /**
     * @access public
     * @param  string
     * @return string
     */
    function & read($name)
    {
        $data = null;

        $fp = @fsockopen($this->server, $this->port, $errno, $errstr, $this->timeout);
        if ($fp === false) {
            error_log(__CLASS__ . '->read() - errono:' . $errno . ' errstr:' . $errstr);
            return $data;
        }
        stream_set_timeout($fp, $this->timeout);

        @list($key, $dummy) = explode(' ', trim($name));
        fwrite($fp, "get {$key}\r\n");
        $values = explode(' ', trim(fgets($fp)));

        if (count($values) !== 1 && $values[0] === 'END') {
            fclose($fp);
            $fp = null;
            return $data;
        }
        if (count($values) !== 4 || $values[0] !== 'VALUE' || !is_numeric($values[3])) {
            error_log(__CLASS__ . '->read() - Unknown header format ' . implode(' ', $values));
            fclose($fp);
            $fp = null;
            return $data;
        }

        $data = fread($fp, ((int) $values[3]));
        $end = fread($fp, 7);
        if ($end !== "\r\nEND\r\n") {
            error_log(__CLASS__ . '->read() - Invalid value length for the key ' . $key);
            $data = null;
        }

        fclose($fp);
        $fp = null;

        return $data;
    }

    /**
     * @access public
     * @param  string
     * @param  string
     */
    function write($name, &$data)
    {
    }

    /**
     * @access public
     * @param  string
     * @param  integer will be ignored
     */
    function delete($name, $lifetime = 0)
    {
    }

    /**
     * @access public
     * @param  integer
     */
    function destroy($lifetime = 0)
    {
    }
}

