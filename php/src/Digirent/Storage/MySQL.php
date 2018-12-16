<?php

require_once 'Digirent/Storage/Abstract.php';

/**
 * Digirent_Storage_MySQL
 *
 * This storage requires the following table.
 * <pre>
 * CREATE TABLE storage (
 *   storage_name varchar(64) NOT NULL default '',
 *   storage_id varchar(64) NOT NULL default '',
 *   storage_time int(11) NOT NULL default '0',
 *   storage_data text NOT NULL
 * );
 * </pre>
 *
 * SYNOPSIS:
 * <code>
 * $options = array(
 *     'hostspec' => 'localhost',
 *     'username' => 'db_user',
 *     'password' => 'db_passwd',
 *     'database' => 'db_database',
 *     'table'    => 'storage',
 * );
 * $storage =& Digirent_Storage::factory('MySQL', $options);
 * </code>
 *
 * @package Digirent_Storage
 */
class Digirent_Storage_MySQL extends Digirent_Storage_Abstract
{
    /**
     * @access private
     * @var    array
     */
    var $options = array();

    /**
     * @access private
     * @var    resource
     */
    var $connection;

    /**
     * @access public
     * @param  array
     */
    function Digirent_Storage_MySQL($options = array())
    {
        parent::Digirent_Storage_Abstract();

        $this->options = $options;
    }

    /**
     * @access private
     * @return resource
     */
    function & getConnection()
    {
        if ($this->connection === null) {
            $this->connection = mysql_connect($this->options['hostspec'],
                                              $this->options['username'],
                                              $this->options['password']);
            if (!mysql_select_db($this->options['database'])) {
                trigger_error(mysql_error(), E_USER_ERROR);
                exit;
            }

            if (isset($this->options['initQueries'])) {
                $sqls = $this->options['initQueries'];
                if (!is_array($sqls)) {
                    $sqls = explode(';', $sqls);
                }
                foreach ($sqls as $sql) {
                    if (($sql = (string) $sql) === '') { continue; }
                    if (!mysql_query($sql, $this->connection)) {
                        trigger_error(mysql_error(), E_USER_ERROR);
                        exit;
                    }
                }
            }
        }
        return $this->connection;
    }

    /**
     * @access public
     * @param  resource
     */
    function setConnection(&$connection)
    {
        $this->connection =& $connection;
    }

    /**
     * @access public
     * @param  string
     */
    function getTable()
    {
        return isset($this->options['table']) ? $this->options['table'] : 'storage';
    }

    /**
     * @access public
     * @param  string
     * @return string
     */
    function & read($name)
    {
        $connection = $this->getConnection();
        $table = $this->getTable();

        $data = null;

        $name = mysql_escape_string($name);
        $sql = "SELECT `{$table}_data` FROM {$table} WHERE `{$table}_name` = '{$name}' LIMIT 1";
        if (!$result = mysql_query($sql, $connection)) {
            trigger_error(mysql_error(), E_USER_ERROR);
            exit;
        }
        if ($row = mysql_fetch_assoc($result)) {
            $data =  base64_decode($row["{$table}_data"]);
        }

        return $data;
    }

    /**
     * @access public
     * @param  string
     * @param  string
     */
    function write($name, $data)
    {
        $connection = $this->getConnection();
        $table = $this->getTable();

        $name = mysql_escape_string($name);
        $sql = "DELETE FROM {$table} WHERE `{$table}_name` = '{$name}'";
        if (!mysql_query($sql, $connection)) {
            trigger_error(mysql_error(), E_USER_ERROR);
            exit;
        }

        $sql = "INSERT INTO {$table} (`{$table}_name`, `{$table}_data`, `{$table}_time`) "
             . sprintf(" VALUES ('%s', '%s', NOW())", $name, mysql_escape_string(base64_encode($data)));
        if (!mysql_query($sql, $connection)) {
            trigger_error(mysql_error(), E_USER_ERROR);
            exit;
        }
    }

    /**
     * @access public
     * @param  string
     * @param  integer 
     */
    function delete($name, $lifetime = null)
    {
        $connection = $this->getConnection();
        $table = $this->getTable();

        $name = mysql_escape_string($name);
        $sql = "DELETE FROM {$table} WHERE `{$table}_name` = '{$name}'";
        if ($lifetime !== null) {
            $datetime = date('Y-m-d H:i:s', time() - $lifetime);
            $sql .= " AND `{$table}_time` <= '{$datetime}'";
        }
        if (!mysql_query($sql, $connection)) {
            trigger_error(mysql_error(), E_USER_ERROR);
            exit;
        }
    }

    /**
     * @access public
     * @param  integer
     */
    function destroy($lifetime = 0)
    {
        $connection = $this->getConnection();
        $table = $this->getTable();

        $datetime = date('Y-m-d H:i:s', time() - $lifetime);

        $sql = "DELETE FROM {$table} WHERE `{$table}_time` <= '{$datetime}'";
        if (!mysql_query($sql, $connection)) {
            trigger_error(mysql_error(), E_USER_ERROR);
            exit;
        }
    }
}

