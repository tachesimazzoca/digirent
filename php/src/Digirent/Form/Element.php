<?php

/**
 * HTML form element
 *
 * @package Digirent_Form
 */
class Digirent_Form_Element
{
    /**
     * @access private
     * @var    mixed 
     */
    var $value = '';

    /**
     * @access private
     * @var    array
     */
    var $options = array();

    /**
     * @access public
     */
    function Digirent_Form_Element()
    {
    }

    /**
     * @access public
     * @return mixed
     */
    function getValue()
    {
        return $this->value;
    }

    /**
     * @access public
     * @param  mixed
     */
    function setValue($value)
    {
        if (is_array($value)) {
            $this->value = array();
            for ($i = 0; $i < count($value); $i++) {
                $this->value[] = (string) $value[$i];
            }
        } else {
            $this->value = (string) $value;
        }
    }

    /**
     * @access public
     * @return array
     */
    function getOptions()
    {
        return $this->options;
    }

    /**
     * @access public
     * @param  array
     * @param  array
     */
    function setOptions($values, $outputs = null)
    {
        $this->options = array(); 

        if ($outputs === null) {
            $outputs = array_values($values);
            $values  = array_keys($values);
        }

        for ($i = 0; $i < count($values); $i++) {
            $key   = (string) $values[$i];
            $value = (string) $outputs[$i];
            $this->options[$key] = $value; 
        }
    }
}

