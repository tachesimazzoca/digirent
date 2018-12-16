<?php

define('DIGIRENT_DB_RESULT_FETCHMODE_ORDERED', 1);
define('DIGIRENT_DB_RESULT_FETCHMODE_ASSOC'  , 2);
define('DIGIRENT_DB_RESULT_FETCHMODE_OBJECT' , 3);

require_once 'Digirent/Db/Dxo/Camelize.php';

/**
 * Digirent_Db_Result_Abstract
 *
 * The result set object.
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
 * // Fetch as an array.
 * $result->setFetchModeOrdered();
 * while ($array =& $result->next()) {
 *     var_dump($array);
 * }
 *
 * // Fetch as an associative array.
 * $result->setFetchModeAssoc();
 * while ($assoc =& $result->next()) {
 *     var_dump($assoc);
 * }
 *
 * // Fetch as the "Emp" object with the class name of the data exchange object.
 * $result->setFetchModeObject('Emp', 'EmpDxo');
 * while ($emp =& $result->next()) {
 *     var_dump($emp);
 * }
 *
 * // The default data exchange object is "Digirent_Db_Dxo_Camelize". 
 * // The object inject the column values into the "Emp" object via the setter method like "Emp::setEmpno".
 * while ($emp =& $result->next()) {
 *     var_dump($emp);
 * }
 * </code>
 *
 * @package Digirent_Db
 * @see     Digirent_Db_Dxo_Camelize
 */
class Digirent_Db_Result_Abstract
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
     * @access protected 
     * @var    integer 
     */
    var $fetchMode;

    /**
     * @access protected 
     * @var    integer 
     */
    var $count;

    /**
     * @access protected 
     * @var    integer 
     */
    var $counter = 0;

    /**
     * @access protected 
     * @var    string 
     */
    var $dtoName;

    /**
     * @access protected 
     * @var    string 
     */
    var $dxoName;

    /**
     * @access public 
     */
    function Digirent_Db_Result_Abstract()
    {
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
    function setEncoding($value)
    {
        $this->encoding = (string) $value;
    }

    /**
     * @access public 
     * @return integer
     */
    function getFetchMode()
    {
        return $this->fetchMode;
    }

    /**
     * @access public 
     * @param  integer 
     * @param  string
     * @param  string
     */
    function setFetchMode($mode, $dtoName = '', $dxoName = '')
    {
        $this->fetchMode = (int) $mode;
        $this->dtoName   = (string) $dtoName;
        $this->dxoName   = (string) $dxoName;
    }

    /**
     * @access public 
     */
    function setFetchModeOrdered()
    {
        $this->setFetchMode(DIGIRENT_DB_RESULT_FETCHMODE_ORDERED);
    }

    /**
     * @access public 
     */
    function setFetchModeAssoc()
    {
        $this->setFetchMode(DIGIRENT_DB_RESULT_FETCHMODE_ASSOC);
    }

    /**
     * @access public 
     * @param  string
     * @param  string
     */
    function setFetchModeObject($dtoName = '', $dxoName = '')
    {
        $this->setFetchMode(DIGIRENT_DB_RESULT_FETCHMODE_OBJECT, $dtoName, $dxoName);
    }

    /**
     * @access public 
     * @return integer FALSE on error.
     */
    function getCount()
    {
        if ($this->count === null) {
            $this->count = $this->numRows();
        }
        return $this->count;
    }

    /**
     * @access public 
     * @return integer 
     */
    function getCounter()
    {
        return (int) $this->counter;
    }

    /**
     * @access public 
     * @param  integer 
     */
    function setCounter($value)
    {
        $this->counter = (int) $value;
    }

    /**
     * @access public 
     * @return integer FALSE on error.
     */
    function numRows()
    {
    }

    /**
     * @access public 
     * @return integer FALSE on error.
     */
    function numCols()
    {
    }

    /**
     * @access public 
     * @param  integer
     * @return array 
     */
    function & fetchArray($counter = null)
    {
    }

    /**
     * @access public 
     * @param  integer
     * @return array 
     */
    function & fetchAssoc($counter = null)
    {
    }

    /**
     * @access public 
     */
    function freeResult()
    {
    }

    /**
     * @access public 
     * @param  integer 
     */
    function offset($counter)
    {
        $this->setCounter($counter);
    }

    /**
     * @access public 
     */
    function rewind()
    {
        $this->setCounter(0);
    }

    /**
     * @access public 
     * @return mixed
     */
    function & first()
    {
        $this->counter = 0;
        $row =& $this->next();
        return $row;
    }

    /**
     * @access public 
     * @return mixed
     */
    function & next()
    {
        $row =& $this->fetch();
        return $row;
    }

    /**
     * @access public 
     * @return mixed
     */
    function & current()
    {
        $row =& $this->fetch(false);
        return $row;
    }

    /**
     * @access public 
     * @param  boolean 
     * @return mixed
     */
    function & fetch($next = true)
    {
        $row = null;

        if ($next) { $this->counter++; }
        if (!$this->counter) { return $row; }

        $params =& $this->fetchAssoc($this->counter - 1);
        if (!$params) { return $row; }

        $external = (string) $this->getEncoding();
        $internal = (string) mb_internal_encoding();

        if ($internal !== $external) {
            foreach ($params as $key => $value) {
                if (is_string($value)) {
                    $value = mb_convert_encoding($value, $internal, $external);
                }
                $params[$key] = $value;
            }
        }

        $fetchMode = $this->getFetchMode();

        if ($fetchMode === DIGIRENT_DB_RESULT_FETCHMODE_OBJECT) {
            if (($dtoName = (string) $this->dtoName) !== '') {
                if ((string) $this->dxoName === '') {
                    $dxoName = 'Digirent_Db_Dxo_Camelize';
                } else {
                    $dxoName = $this->dxo;
                }
                $dxo = new $dxoName();
                $row = new $dtoName();
                $dxo->convert($params, $row);
            } else {
                $row = new stdClass();
                foreach ($params as $key => $value) {
                    $row->$key = $value;
                }
            }

        } elseif ($fetchMode === DIGIRENT_DB_RESULT_FETCHMODE_ORDERED) {
            $row = array_values($params);

        } else {
            $row = $params;

        }

        return $row;
    }
}

