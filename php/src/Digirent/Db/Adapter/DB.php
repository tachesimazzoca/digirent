<?php

require_once 'Digirent/Db/Adapter/Abstract.php';
require_once 'Digirent/Db/Result/DB.php';

require_once 'DB.php';

/**
 * Digirent_Db_Adapter_DB
 *
 * The database adapter based on the PEAR "DB".
 *
 * @package Digirent_Db
 */
class Digirent_Db_Adapter_DB extends Digirent_Db_Adapter_Abstract
{
    /**
     * @access private
     * @var    string
     */
    var $dsn;

    /**
     * @access private
     * @var    array
     */
    var $options = array();

    /**
     * @access private
     * @var    object
     */
    var $db;

    /**
     * @access private
     * @var    string
     */
    var $charset;

    /**
     * @access private
     * @var    array
     */
    var $initQueries = array();

    /**
     * @access public
     * @param  array
     */
    function Digirent_Db_Adapter_DB($params = array())
    {
        parent::Digirent_Db_Adapter_Abstract();

        if (isset($params['dsn'])) {
            if (is_array($params['dsn'])) {
                $this->dsn = sprintf(
                    "%s://%s:%s@%s/%s"
                    , $params['phptype']
                    , $params['username']
                    , $params['password']
                    , $params['hostspec']
                    , $params['database']
                );
            } else {
                $this->dsn = (string) $params['dsn'];
            }
        }
        if (isset($params['options'])) {
            $this->options = (array) $params['options'];
        }
    }

    /**
     * @access public
     * @param  string
     */
    function setDsn($value)
    {
        $this->dsn = (string) $value;
    }

    /**
     * @access public
     * @param  array
     */
    function setOptions($params)
    {
        $this->options = (array) $params;
    }

    /**
     * @access public
     * @param  string
     */
    function setCharset($value)
    {
        $this->charset = (string) $value;
    }

    /**
     * @access public
     * @param  array
     */
    function setInitQueries($value)
    {
        $this->initQueries = (array) $value;
    }

    /**
     * @access public
     * @return boolean
     */
    function connect()
    {
        $this->messages = array();

        if (!$this->db) {

            $options = is_array($this->options) ? $this->options : (bool) $this->options;

            $this->db =& DB::connect($this->dsn, $options);

            if (DB::isError($this->db)) {
                $this->messages[] = $this->db->getMessage();
                $this->messages[] = $this->db->getUserInfo();
                $this->onError();
                return false;
            }

            if ((string) $this->charset !== '') {
                if ((string) $this->db->phptype === 'mysql') {
                    if (function_exists('mysql_set_charset')) {
                        mysql_set_charset($this->charset);
                    }
                }
            }
    
            foreach ($this->initQueries as $sql) {
                $result =& $this->db->query($sql);
                if (DB::isError($result)) {
                    $this->messages[] = $result->getMessage();
                    $this->messages[] = $result->getUserInfo();
                    $this->onError();
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @access public
     * @return boolean
     */
    function disconnect()
    {
        $result = true;

        if (is_a($this->db, 'DB')) {
            $result = $this->db->disconnect();
        }
        $this->db = null;

        return $result;
    }

    /**
     * @access public
     * @return integer FALSE on error.
     */
    function affectedRows()
    {
        if (!$this->connect()) {
            return false;
        }

        $this->messages = array();

        $result = $this->db->affectedRows();
        if (DB::isError($result)) {
            $this->messages[] = $result->getMessage();
            $this->messages[] = $result->getUserInfo();
            $this->onError();
            return false;
        }

        return $result;
    }

    /**
     * @access public
     * @param  string
     * @param  array
     * @return boolean
     */
    function insert($table, $bind)
    {
        if (!$this->connect()) {
            return false;
        }

        $this->messages = array();

        $this->encode($bind);

        $result = $this->db->autoExecute($table, $bind, DB_AUTOQUERY_INSERT);
        if (DB::isError($result)) {
            $this->messages[] = $result->getMessage();
            $this->messages[] = $result->getUserInfo();
            $this->onError();
            return false;
        }

        return true;
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
        if (!$this->connect()) {
            return false;
        }

        $this->messages = array();

        $this->encode($bind);

        $result = $this->db->autoExecute($table, $bind, DB_AUTOQUERY_UPDATE, $where);

        if (DB::isError($result)) {
            $this->messages[] = $result->getMessage();
            $this->messages[] = $result->getUserInfo();
            $this->onError();
            return false;
        }

        return true;
    }

    /**
     * @access public
     * @param  string
     * @param  array
     * @param  array
     * @return object FALSE on error.
     */
    function & query($sql, $bind = array(), $limit = array())
    {
        $result = false;

        if (!$this->connect()) {
            return $result;
        }

        $this->messages = array();

        $this->encode($bind);

        if (count($limit) === 2) {
            $dbResult =& $this->db->limitQuery($sql, $limit[0], $limit[1], $bind);
        } else if (count($limit) === 1) {
            $dbResult =& $this->db->limitQuery($sql, 0, $limit[0], $bind);
        } else {
            $dbResult =& $this->db->query($sql, $bind);
        }

        if (DB::isError($dbResult)) {
            $this->messages[] = $dbResult->getMessage();
            $this->messages[] = $dbResult->getUserInfo();
            $this->onError();
            return $result;
        }

        if (is_object($dbResult) && strtolower(get_class($dbResult)) == 'db_result') {
            $result = new Digirent_Db_Result_DB($dbResult);
            $result->setEncoding($this->getEncoding());
        } else {
            $result = true;
        }

        return $result;
    }

    /**
     * @access public
     * @param  string
     * @return string
     */
    function escape($value)
    {
        if (!$this->connect()) {
            return false;
        }
        return $this->db->escapeSimple($value);
    }

    /**
     * @access public
     * @param  string
     * @return string
     */
    function quoteIdentifier($value)
    {
        if (!$this->connect()) {
            return false;
        }
        return $this->db->quoteIdentifier($value);
    }

    /**
     * @access public
     * @param  string
     * @return string
     */
    function quoteSmart($value)
    {
        if (!$this->connect()) {
            return false;
        }
        return $this->db->quoteSmart($value);
    }

    /**
     * @access public
     * @return string 
     */
    function lastInsertId()
    {
        if (!$this->connect()) {
            return false;
        }

        $id = false;

        $this->messages = array();
        if ((string) $this->db->phptype === 'mysql') {
            $result =& $this->db->query('SELECT LAST_INSERT_ID()');
            if (DB::isError($result)) {
                $this->messages[] = $result->getMessage();
                $this->messages[] = $result->getUserInfo();
            } else {
                $row = $result->fetchRow(DB_FETCHMODE_ORDERED);
                $id = (string) $row[0];
            }
        } else {
            $this->messages[] = 'Digirent_Db_Adapter_DB::lastInsertId() does not support.';
        }

        if ($id === false) {
            $this->onError();
        }

        return $id;
    }
}

