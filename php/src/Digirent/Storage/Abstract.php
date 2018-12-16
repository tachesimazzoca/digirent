<?php

/**
 * Digirent_Storage_Abstract
 *
 * @package Digirent_Storage
 */
class Digirent_Storage_Abstract
{
    /**
     * @access public
     */
    function Digirent_Storage_Abstract()
    {
    }

    /**
     * @access public
     * @param  string
     * @return string
     */
    function & read($key)
    {
    }

    /**
     * @access public
     * @param  string
     * @param  string
     */
    function write($key, &$data)
    {
    }

    /**
     * @access public
     * @param  string
     * @param  integer
     */
    function delete($key, $lifetime = 0)
    {
    }

    /**
     * @access public
     * @param  integer 
     */
    function destroy($lifetime = 0)
    {
    }

    /**
     * @access public
     * @param  integer  the length of a record ID.
     * @return string   unique ID 
     */
    function create($length = 32)
    {
        $n = 0;
        do {
            if ($n > 1000) {
                trigger_error(__CLASS__ . '::create() could not generate unique ID.', E_USER_ERROR);
                exit;
            }
            list($usec, $sec) = split(' ', microtime());
            srand((float)$sec + (float)$usec * 100000);
            $name = substr(sha1(microtime().rand()), 0, $length);
            $n++;
        } while($this->read($name) !== null);

        $data = '';
        $this->write($name, $data);

        return $name;
    }
}

