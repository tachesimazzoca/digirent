<?php

require_once 'Digirent/Db/Result/Abstract.php';

/**
 * Digirent_Db_Result_Mock
 *
 * The result set object for the mock.
 *
 * @package Digirent_Db
 */
class Digirent_Db_Result_Mock extends Digirent_Db_Result_Abstract
{
    /**
     * @access private
     * @var    array
     */
    var $rows = array();

    /**
     * @access public 
     */
    function Digirent_Db_Result_Mock()
    {
        parent::Digirent_Db_Result_Abstract();
    }

    /**
     * @access public 
     * @param  array
     */
    function addRow($row)
    {
        $this->rows[] = $row;
    }

    /**
     * @access public 
     * @param  array
     */
    function setRows(&$rows)
    {
        $this->rows =& $rows;
    }

    /**
     * @access public 
     * @param  array
     */
    function clearRows()
    {
        $this->rows = array();
    }

    /**
     * @access public 
     * @return integer 
     */
    function numRows()
    {
        return count($this->rows);
    }

    /**
     * @access public 
     * @return integer 
     */
    function numCols()
    {
        return count(array_keys((array) @$this->rows[0]));
    }

    /**
     * @access public 
     * @param  integer
     * @return array
     */
    function & fetchArray($counter = null)
    {
        $array = null;
        if (isset($this->rows[$counter])) {
            $array = array_values($this->rows[$counter]);
        }
        return $array;
    }

    /**
     * @access public 
     * @param  integer
     * @return array
     */
    function & fetchAssoc($counter = null)
    {
        $assoc = null;
        if (isset($this->rows[$counter])) {
            $assoc = $this->rows[$counter];
        }
        return $assoc;
    }
}

