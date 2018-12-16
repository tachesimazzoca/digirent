<?php

require_once 'Digirent/Db/Result/Abstract.php';

require_once 'DB.php';

/**
 * Digirent_Db_Result_DB
 *
 * The result set object for the PEAR "DB".
 *
 * @package Digirent_Db
 */
class Digirent_Db_Result_DB extends Digirent_Db_Result_Abstract
{
    /**
     * @access private
     * @var    object 
     */
    var $result;

    /**
     * @access public 
     * @param  object  DB_Result 
     */
    function Digirent_Db_Result_DB(&$result)
    {
        parent::Digirent_Db_Result_Abstract();

        $this->result =& $result;
    }

    /**
     * @access public 
     * @return integer FALSE on error.
     */
    function numRows()
    {
        $this->messages = array();

        $result = $this->result->numRows();
        if (DB::isError($result)) {
            $this->messages[] = $result->getMessage();
            $this->messages[] = $result->getUserInfo();
            return false;
        }

        return $result;
    }

    /**
     * @access public 
     * @return integer FALSE on error.
     */
    function numCols()
    {
        $this->messages = array();

        $result = $this->result->numCols();
        if (DB::isError($result)) {
            $this->messages[] = $result->getMessage();
            $this->messages[] = $result->getUserInfo();
            return false;
        }

        return $result;
    }

    /**
     * @access public 
     * @param  integer 
     * @return array 
     */
    function & fetchArray($counter = null)
    {
        $array =& $this->result->fetchRow(DB_FETCHMODE_ORDERED, $counter);
        return $array;
    }

    /**
     * @access public 
     * @param  integer 
     * @return mixed
     */
    function & fetchAssoc($counter = null)
    {
        $assoc =& $this->result->fetchRow(DB_FETCHMODE_ASSOC, $counter);
        return $assoc;
    }

    /**
     * @access public 
     */
    function freeResult()
    {
        if ($this->result !== null) {
            $this->result->free();
        }
        $this->result = null;
    }

    /**
     * @deprecated 
     * @access public 
     * @return boolean 
     */
    function hasResult()
    {
        return (bool) (strtolower(get_class($this->result)) == 'db_result');
    }
}

