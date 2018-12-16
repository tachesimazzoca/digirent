<?php

/**
 * Digirent_Session_Handler
 *
 * SYNOPSIS:
 * <code>
 *
 * // use Digirent_Session_Handler_DB 
 * $params = array(
 *     'dsn' => 'mysql://username:password@hostspec/database',
 * );
 * Digirent_Sesssion_Handler::set('DB', $params);
 * </code>
 *
 * @package Digirent_Session_Handler
 * @see Digirent_Session_Handler_DB
 */
class Digirent_Session_Handler
{
    /**
     * @static
     * @access public
     * @param  string 
     * @param  array 
     */
    function set($name = '', $params = array())
    {
        static $handler;

        require_once "Digirent/Session/Handler/{$name}.php";
        $classname = "Digirent_Session_Handler_{$name}";
        $handler = new $classname($params);

        session_set_save_handler(
            array( &$handler, 'open'    ),
            array( &$handler, 'close'   ),
            array( &$handler, 'read'    ),
            array( &$handler, 'write'   ),
            array( &$handler, 'destroy' ),
            array( &$handler, 'gc'      )
        );
    }
}

