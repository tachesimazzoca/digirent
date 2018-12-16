<?php

/**
 * Digirent_Db_Pager
 *
 * The pager support object for the Digirent_Db_Result_* object.
 *
 * SYNOPSIS:
 * <code>
 * $adapter =& Digirent_Db::factory('DB', array('dsn' => 'mysql://username:password@localhost/database'));
 *
 * $pager = new Digirent_Db_Pager($adapter);
 *
 * // Select 20 records from the offset 10.
 * $pager->setOffset(10);
 * $pager->setLimit(20);
 * $result =& $pager->query('SELECT * FROM emp WHERE deptno = ?', array(1));
 *
 * if ($result === false) {
 *     trigger_error(implode("\n", $adapter->getMessages()));
 *     exit;
 * }
 *
 * echo 'TOTAL: ' . $pager->getCount();
 * while ($row =& $result->next()) {
 *     var_dump($row);
 * }
 * </code>
 *
 * @package Digirent_Db
 */
class Digirent_Db_Pager
{
    /**
     * @access private
     * @var    object
     */
    var $adapter;

    /**
     * @access private
     * @var    integer 
     */
    var $count = 0;

    /**
     * @access private
     * @var    integer 
     */
    var $offset = 0;

    /**
     * @access private
     * @var    integer 
     */
    var $limit = 0;

    /**
     * @access public 
     */
    function Digirent_Db_Pager()
    {
    }

    /**
     * @access public 
     * @param  object
     */
    function setAdapter(&$adapter)
    {
        $this->adapter =& $adapter;
    }

    /**
     * @access public 
     * @return integer
     */
    function getCount()
    {
        return (int) $this->count;
    }

    /**
     * @access public 
     * @param  integer
     */
    function setCount($value)
    {
        $this->count = (int) $value;
    }

    /**
     * @access public 
     * @return integer
     */
    function getOffset()
    {
        return (int) $this->offset;
    }

    /**
     * @access public 
     * @param  integer
     */
    function setOffset($value)
    {
        $this->offset = (int) $value;
    }

    /**
     * @access public 
     * @return integer
     */
    function getLimit()
    {
        return (int) $this->limit;
    }

    /**
     * @access public 
     * @param  integer
     */
    function setLimit($value)
    {
        $this->limit = (int) $value;
    }

    /**
     * @access public 
     * @param  string 
     * @param  arrary 
     * @param  array 
     * @param  boolean 
     * @return mixed
     */
    function & query($sql, $bind = array(), $limit = array(), $fix = true)
    {
        $this->count = 0;

        if ($countQuery = $this->_rewriteCountQuery($sql)) {
            if (($result =& $this->adapter->query($countQuery, $bind)) === false) {
                // Query failed.
                return $result;
            }
            $result->setFetchModeOrdered();
            if ($row = $result->next()) {
                $this->count = (int) $row[0];
            }
            $result->freeResult();
        } else {
            $result =& $this->adapter->query($sql, $bind, $limit);
            if (!$result) {
                trigger_error(implode(',', $this->adapter->getMessages()), E_USER_ERROR);
                exit;
            }
            if (($count = $result->getCount()) === false) {
                // The getCount() method does not support.
                trigger_error(implode(',', $result->getMessages()), E_USER_ERROR);
                exit;
            }
            $this->count = $count;
        }

        $offset = array();

        if (count($limit) !== 2) {
            if (count($limit) === 1) {
                $limit = array(0, $limit[0]);
            } else {
                // TODO: define('DIGIRENT_DB_PAGER_LIMIT_MAX', ....);
                $limit = array(0, 99999999);
            }
        }

        $this->count -= $limit[0];
        if ($limit[1] < $this->count) {
            $this->count = $limit[1];
        }

        $offset = array($this->offset + $limit[0]);
        if ($fix && $this->count > 0 && $this->limit > 0) {
            // fix last offset
            if ($this->offset >= $this->count) {
                $this->offset = (int) floor(($this->count - 1) / $this->limit) * $this->limit;
            }
            $offset = array($this->offset + $limit[0]);
        }

        if ($limit[1] < $this->limit) {
            $offset[] = $limit[1];
        } else {
            $offset[] = ($this->limit < 0) ? $limit[1] : $this->limit; 
        }

        $result =& $this->adapter->query($sql, $bind, $offset);

        return $result;
    }

    /**
     * @access private 
     * @param  string 
     * @return string 
     */
    function _rewriteCountQuery($sql)
    {
        // has DISTINCT or GROUP BY.
        if (preg_match('/^\s*SELECT\s+\bDISTINCT\b/is', $sql) || preg_match('/\s+GROUP\s+BY\s+/is', $sql)) {
            return '';
        }
        // has sub query in SELECT.
        $subquery = '(?:\().*\bFROM\b.*(?:\))';
        $pattern = '/(?:.*'.$subquery.'.*)\bFROM\b\s+/Uims';
        if (preg_match($pattern, $sql)) {
            return '';
        }
        // has sub query with (LIMIT|ORDER).
        $subquery = '(?:\().*\b(LIMIT|ORDER)\b.*(?:\))';
        $pattern = '/.*\bFROM\b.*(?:.*'.$subquery.'.*).*/Uims';
        if (preg_match($pattern, $sql)) {
            return '';
        }

        // rewrite to "COUNT(*)" query.
        $query = preg_replace('/(?:.*)\bFROM\b\s+/Uims', 'SELECT COUNT(*) FROM ', $sql, 1);
        list($query, ) = preg_split('/\s+ORDER\s+BY\s+/is', $query);
        list($query, ) = preg_split('/\bLIMIT\b/is', $query);

        return trim($query);
    }
}

