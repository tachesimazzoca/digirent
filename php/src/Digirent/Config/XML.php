<?php

require_once 'Digirent/Config/Abstract.php';

require_once 'XML/Unserializer.php';

/**
 * Digirent_Config_XML
 *
 * /path/to/config.xml
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?> 
 * <config> 
 *     <section name="url">
 *         <param name="domain">example.net</param>
 *         <param name="base">/</param>
 *     </section>
 *     <section name="test:url">
 *         <param name="domain">test.example.net</param>
 *     </section>
 * </config>
 * </code>
 *
 * @package Digirent_Config
 */
class Digirent_Config_XML extends Digirent_Config_Abstract
{
    /**
     * @access private
     * @param  string
     */
    var $path;

    /**
     * @access private
     * @param  string
     */
    var $extend;

    /**
     * @access public
     * @param  string
     * @param  string
     */
    function Digirent_Config_XML($path, $extend = null)
    {
        parent::Digirent_Config_Abstract();

        $this->path   = $path;
        $this->extend = $extend;
    }

    /**
     * @access protected
     * @return array
     */
    function & handleLoadParams()
    {
        $params = array();

        $unserializer = new XML_Unserializer(
            array('complexType'     => 'array',
                  'parseAttributes' => true,
                  'attributesArray' => '_attributes',
                  'forceEnum'       => array('section', 'param')));

        $result = $unserializer->unserialize($this->path, true);
        if (PEAR::isError($result)) {
            trigger_error($result->getMessage());
            return $params;
        }

        $elements = $unserializer->getUnserializedData();
        if (PEAR::isError($elements)) {
            trigger_error($elements->getMessage());
            return $params;
        }

        foreach ($elements['section'] as $section) {
            if (($name = (string) @$section['_attributes']['name']) === '') {
                continue;
            }
            if (!isset($params[$name])) {
                $params[$name] = array();
            }
            if (!is_array($section['param'])) {
                continue;
            }
            foreach ($section['param'] as $vars) {
                if (($key = (string) @$vars['_attributes']['name']) === '') {
                    continue;
                }
                $value = mb_convert_encoding(@$vars['_content'], mb_internal_encoding(), 'UTF-8');
                $params[$name][$key] = $value;
            }
        }

        return $params;
    }
}

