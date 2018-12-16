<?php

/**
 * Digirent_Storage
 *
 * SYNOPSIS:
 * <code>
 * $options = array(
 *     'directory' => '/path/to/storage/dir/',
 *     'mode'      => 0777,
 * );
 * $storage =& Digirent_Storage::factory('File', $options);
 *
 * $storage->write('message', 'Hello World!');
 * $data = $storage->get('message'); // "Hello World!"
 *
 * // create a new record with a unique identifier.
 * $uniqueId = $storage->create();
 * $params = array('foo' => 'bar');
 * $storage->write($uniqueId, serialize($params));
 * $data = unserialize($storage->get($uniqueId)); // array('foo' => 'bar')
 * </code>
 *
 * @package Digirent_Storage
 */
class Digirent_Storage
{
    /**
     * @static
     * @access public
     * @param  string
     * @param  array 
     */
    function & factory($name, $options = array())
    {
        require_once "Digirent/Storage/{$name}.php";
        $classname = "Digirent_Storage_{$name}";
        $instance = new $classname($options);

        return $instance;
    }
}

