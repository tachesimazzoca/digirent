<?php

/**
 * Digirent_Db
 *
 * SYNOPSIS:
 * <code>
 * $adapter =& Digirent_Db::factory('DB', array('dsn' => 'mysql://username:password@localhost/database'));
 *
 * $result =& $adapter->query('SELECT * FROM emp WHERE deptno = ?', array(1));
 * if ($result === false) {
 *     trigger_error(implode("\n", $adapter->getMessages()));
 *     exit;
 * }
 *
 * while ($row =& $result->next()) {
 *     var_dump($row);
 * }
 * </code>
 *
 * @package Digirent_Db
 */
class Digirent_Db
{
    /**
     * @static
     * @access public
     * @param  string
     * @param  array
     */
    function & factory($name, $params = array())
    {
        $classname = "Digirent_Db_Adapter_{$name}";
        $classfile = str_replace('_', '/', $classname) . '.php';

        require_once "$classfile";
        $instance = new $classname($params);

        return $instance;
    }
}

