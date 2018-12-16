<?php

require_once 'Digirent/Session/Handler/Abstract.php';
require_once 'DB.php';

/**
 * Digirent_Session_Handler_DB
 *
 * PEAR DB session handler
 *
 * This session handler requires the following table. 
 * <pre>
 * CREATE TABLE sessions (
 *   sess_name varchar(64) NOT NULL default '',
 *   sess_id varchar(64) NOT NULL default '',
 *   sess_time int(11) NOT NULL default '0',
 *   sess_data text NOT NULL
 * );
 * </pre>
 *
 * SYNOPSIS:
 * <code>
 * $params = array(
 *     // DB::connect DSN
 *     'dsn' => 'mysql://username:password@hostspec/database',
 *     // DB::connect options. 
 *     'options' => array(),
 *     // the table name for the session. 'sessions' as default.
 *     'table' => 'sessions',
 * );
 * $handler = new Digirent_Session_Handler_DB($params); 
 *
 * session_set_save_handler(
 *     array( &$handler, 'open'    ),
 *     array( &$handler, 'close'   ),
 *     array( &$handler, 'read'    ),
 *     array( &$handler, 'write'   ),
 *     array( &$handler, 'destroy' ),
 *     array( &$handler, 'gc'      )
 * );
 * </code>
 *
 * @package Digirent_Session_Handler
 */
class Digirent_Session_Handler_DB extends Digirent_Session_Handler_Abstract
{
    /**
     * @access private
     * @var    array
     */
    var $params;

    /**
     * @access private
     * @var    object 
     */
    var $connection;

    /**
     * @access public 
     * @param  array 
     */
    function Digirent_Session_Handler_DB($params = array())
    {
        parent::Digirent_Session_Handler_Abstract();

        $this->params = (array) $params;

        if ((string) @$this->params['table'] === '') {
            $this->params['table'] = 'sessions';
        }
    }

    /**
     * @access private 
     * @return object
     */
    function & getConnection()
    {
        if ($this->connection === null) {
            $this->connection =& DB::connect($this->params['dsn'], (array) @$this->params['options']);
            if (PEAR::isError($this->connection)) {
                trigger_error($this->connection->getMessage(), E_USER_ERROR);
                exit;
            }
            if (isset($this->params['initQueries'])) {
                foreach ((array) $this->params['initQueries'] as $sql) {
                    $error =& $this->connection->query($sql);
                    if (PEAR::isError($error)) {
                        trigger_error($error->getMessage(), E_USER_ERROR);
                        exit;
                    }
                }
            }
        }

        return $this->connection;
    }

    /**
     * @access public
     * @param  string 
     * @return string 
     */
    function read($sid)
    {
        $connection =& $this->getConnection();

        $sql = "SELECT sess_data"
             . " FROM " . $this->params['table']
             . " WHERE sess_id = ? AND sess_name = ?";
        $values = array($sid, $this->sessName());

        $result =& $connection->query($sql, $values);

        if (DB::isError($result)) { return ''; }

        $row =& $result->fetchRow(DB_FETCHMODE_ASSOC, 0);

        $data = base64_decode($row['sess_data']);

        return (string) $data;
    }

    /**
     * @access public
     * @param  string 
     * @param  string 
     * @return boolean 
     */
    function write($sid, $data)
    {
        if ((string) $sid === '') { return false; }

        $connection =& $this->getConnection();

        $sql = "DELETE FROM " . $this->params['table']
             . " WHERE sess_id = ? AND sess_name = ?";
        $values = array($sid, $this->sessName());
        $result =& $connection->query($sql, $values);

        if (DB::isError($result)) { return false; }

        $data = base64_encode($data);

        $sql = "INSERT INTO " . $this->params['table']
             . " (sess_id, sess_name, sess_data, sess_time) VALUES (?, ?, ?, ?)";
        $values = array($sid, $this->sessName(), $data, time());
        $result =& $connection->query($sql, $values);

        if (DB::isError($result)) { return false; }

        return (bool) $result;
    }

    /**
     * @access public
     * @param  string 
     * @return boolean 
     */
    function destroy($sid)
    {
        $connection =& $this->getConnection();

        $sql = "DELETE FROM " . $this->params['table']
             . " WHERE sess_id = ? AND sess_name = ?";
        $values = array((string) $sid, $this->sessName());
        $result =& $connection->query($sql, $values);

        if (DB::isError($result)) { return false; }

        return (bool) $result;
    }

    /**
     * @access public
     * @param  integer 
     * @return boolean 
     */
    function gc($maxlifetime)
    {
        $connection =& $this->getConnection();

        $expire = time() - intval($maxlifetime);

        $sql = "DELETE FROM " . $this->params['table']
             . " WHERE sess_time < ? AND sess_name = ?";
        $values = array($expire, $this->sessName());
        $result =& $connection->query($sql, $values);

        if (DB::isError($result)) { return false; }

        return (bool) $result;
    }
}

