<?php

require_once 'Digirent/Renderer/Smarty.php';

/**
 * Digirent_Renderer_Smarty_Variable
 *
 * The renderer based on the Smarty template engine that use a variable resource.
 *
 * @package Digirent_Renderer
 */
class Digirent_Renderer_Smarty_Variable extends Digirent_Renderer_Smarty
{
    /**
     * @access private
     * @var    string
     */
    var $resourceName = 'var';

    /**
     * @access private
     * @var    integer 
     */
    var $timestamp = null;

    /**
     * @access public
     * @param  array
     */
    function Digirent_Renderer_Smarty_Variable($options = array())
    {
        parent::Digirent_Renderer_Smarty($options);

        $this->registerResource();
    }

    /**
     * @access public
     * @param  string 
     */
    function registerResource($name = 'var')
    {
        $this->resourceName = $name;
        $this->engine->register_resource(
            $this->resourceName,
            array(
                &$this
                , 'smarty_resource_var_source'
                , 'smarty_resource_timestamp'
                , 'smarty_resource_secure'
                , 'smarty_resource_trusted'
            )
        );
    }

    /**
     * @access public
     * @param  integer
     */
    function setTimestamp($value)
    {
        $this->timestamp = is_numeric($value) ? (int) $value : null;
    }

    /**
     * @access public
     */
    function execute()
    {
        $this->result = $this->engine->fetch($this->resourceName . ':' . $this->template);
    }

    /**
     * @access private
     */
    function smarty_resource_var_source($tpl_name, &$source, &$smarty)
    {
        $source = $smarty->get_template_vars($tpl_name);
        $smarty->clear_assign($tpl_name);
        return true;
    }

    /**
     * @access private
     */
    function smarty_resource_timestamp($tpl_name, &$timestamp, &$smarty)
    {
        $timestamp = ($this->timestamp !== null) ? (int) $this->timestamp : time() + 86400;
        return true;
    }

    /**
     * @access private
     */
    function smarty_resource_secure($tpl_name, &$smarty)
    {
         return true;
    }

    /**
     * @access private
     */
    function smarty_resource_trusted($tpl_name, &$smarty_obj)
    {
         return true;
    }
}

