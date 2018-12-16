<?php

require_once 'Digirent/Renderer/Abstract.php';

require_once 'Smarty.class.php';

/**
 * Digirent_Renderer_Smarty
 *
 * The renderer based on the Smarty template engine.
 *
 * @package Digirent_Renderer
 */
class Digirent_Renderer_Smarty extends Digirent_Renderer_Abstract
{
    /**
     * @access private
     * @var    object
     */
    var $engine;

    /**
     * @access public
     * @param  array
     */
    function Digirent_Renderer_Smarty($options = array())
    {
        parent::Digirent_Renderer_Abstract();

        $this->engine = new Smarty();

        if (defined('DIGIRENT_RENDERER_SMARTY_TEMPLATE_DIR')) {
            $this->engine->template_dir  = DIGIRENT_RENDERER_SMARTY_TEMPLATE_DIR;
        }
        if (defined('DIGIRENT_RENDERER_SMARTY_COMPILE_DIR')) {
            $this->engine->compile_dir  = DIGIRENT_RENDERER_SMARTY_COMPILE_DIR;
        }
        if (defined('DIGIRENT_RENDERER_SMARTY_CACHE_DIR')) {
            $this->engine->cache_dir    = DIGIRENT_RENDERER_SMARTY_CACHE_DIR;
        }
        if (defined('DIGIRENT_RENDERER_SMARTY_CONFIG_DIR')) {
            $this->engine->config_dir   = DIGIRENT_RENDERER_SMARTY_CONFIG_DIR;
        }
        if (defined('DIGIRENT_RENDERER_SMARTY_PLUGINS_DIR')) {
            $this->engine->plugins_dir = explode(':', DIGIRENT_RENDERER_SMARTY_PLUGINS_DIR);
        }

        foreach (
            array(
                'template_dir',
                'compile_dir',
                'cache_dir',
                'config_dir',
                'plugins_dir',
            ) as $key
        ) {
            if (array_key_exists($key, $options)) {
                $this->engine->$key = $options[$key];
            }
        }

        $this->extension = 'tpl';
    }

    /**
     * @access public
     */
    function execute()
    {
        $this->result = $this->engine->fetch($this->getTemplatePath());
    }

    /**
     * @access public
     * @param  mixed
     */
    function & getParam($name)
    {
        $value =& $this->engine->get_template_vars($name);

        return $value;
    }

    /**
     * @access public
     * @param  array
     */
    function & getParams()
    {
        return $this->engine->get_template_vars();
    }

    /**
     * @access public
     * @param  string
     * @param  mixed
     */
    function setParam($name, $value)
    {
        $this->engine->assign($name, $value);
    }

    /**
     * @access public
     * @param  string
     * @param  mixed
     */
    function setParamByRef($name, &$value)
    {
        $this->engine->assign_by_ref($name, $value);
    }

    /**
     * @access public
     * @param  string
     */
    function removeParam($name)
    {
        $this->engine->clear_assign($name);
    }

    /**
     * @access public
     */
    function removeParams()
    {
        $this->engine->clear_all_assign();
    }

    /**
     * @access public
     * @param  callback
     */
    function registerPrefilter(&$filter)
    {
        $this->engine->register_prefilter($filter);
    }

    /**
     * @access public
     * @param  callback
     */
    function registerPostfilter(&$filter)
    {
        $this->engine->register_postfilter($filter);
    }

    /**
     * @access public
     * @param  callback
     */
    function registerOutputfilter(&$filter)
    {
        $this->engine->register_outputfilter($filter);
    }

    /**
     * @access public
     * @param  string
     */
    function setLeftDelimiter($delimiter)
    {
        $this->engine->left_delimiter = $delimiter;
    }

    /**
     * @access public
     * @param  string
     */
    function setRightDelimiter($delimiter)
    {
        $this->engine->right_delimiter = $delimiter;
    }
}

