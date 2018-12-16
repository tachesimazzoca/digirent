<?php

/**
 * Digirent_Config
 *
 * SYNOPSIS:
 * <code>
 * // The include path to the configulation file.
 * define('DIGIRENT_CONFIG_DIR', '/path/to/config/dir/');
 * // Replace with the values at the "test:*" section.
 * define('DIGIRENT_CONFIG_NAME', 'test');
 *
 * // Create the singleton instance based on "/path/to/config/dir/common.(ini|xml)".
 * $config =& Digirent_Config::registry('common');
 *
 * var_dump($config->getParams());
 *
 * // The associative array of the "foo" section.
 * var_dump($config->getParam('foo'));
 *
 * // The value of the "bar" key of the "foo" section.
 * var_dump($config->getParam('foo', 'bar'));
 * </code>
 *
 * @package Digirent_Config
 */
class Digirent_Config
{
    /**
     * @access public
     * @param  string
     * @param  boolean  reload on each access.
     * @return object
     */
    function & registry($namespace, $reload = false)
    {
        static $registry;

        if ($registry === null) {
            $registry = array();
        }

        if (!isset($registry[$namespace]) || $reload) {

            $registry[$namespace] = null;

            $directory = (defined('DIGIRENT_CONFIG_DIR'))  ? DIGIRENT_CONFIG_DIR  : '';
            $extend    = (defined('DIGIRENT_CONFIG_NAME')) ? DIGIRENT_CONFIG_NAME : null;

            $filename = str_replace('.', DIRECTORY_SEPARATOR, $namespace);

            $formats = array('ini', 'xml');

            foreach ($formats as $format) {
                $path = $directory . $filename . '.' . $format;
                if (is_readable($path)) {
                    $subclass = ($format === 'ini') ? ucfirst($format) : strtoupper($format);
                    require_once "Digirent/Config/{$subclass}.php";
                    $classname = "Digirent_Config_{$subclass}";
                    $registry[$namespace] = new $classname($path, $extend);
                    break;
                }
            }

        }

        return $registry[$namespace];
    }
}

