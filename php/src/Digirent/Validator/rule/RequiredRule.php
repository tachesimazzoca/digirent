<?php

require_once 'Digirent/Validator/Rule.php';

class RequiredRule extends Digirent_Validator_Rule
{
    var $group = array();

    var $and   = '';
    var $not   = '';
    var $match = array();
    
    function RequiredRule()
    {
        parent::Digirent_Validator_Rule();
    }

    function setGroup($values)
    {
        $this->group = (array) $values;
    }

    function setAnd($value)
    {
        $this->and = $value;
    }

    function setNot($value)
    {
        $this->not = $value;
    }

    function setMatch($values)
    {
        $this->match = (array) $values;
    }

    function check($value)
    {
        if ((string) $this->and !== '') {
            // skip if "and" parameter value is empty.
            if ((string) $this->getParam($this->and) === '') { return true; }
        }
        if ((string) $this->not !== '') {
            // skip if "not" parameter value is not empty.
            if ((string) $this->getParam($this->not) !== '') { return true; }
        }
        if ((string) @$this->match[0] !== '') {
            // skip if "match" parameter value is not matched.
            if ($this->getParam($this->match[0]) !== (string) @$this->match[1]) { return true; }
        }

        $values = array();
        if ($this->group) {
            foreach ($this->group as $name) {
                $values[] = (string) $this->getParam($name);
            }
        } else {
            $values[] = (string) $value;
        }

        foreach ($values as $value) {
            if ($value === '') { return false; }
        }

        return true;
    }
}

