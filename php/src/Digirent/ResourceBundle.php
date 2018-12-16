<?php

/**
 * Digirent_ResourceBundle
 *
 * SYNOPSIS:
 *
 * /path/to/dir/Color.ini
 * <pre>
 * red   = "RED"
 * blue  = "BLUE"
 * </pre>
 *
 * /path/to/dir/Color_fr.ini
 * <pre>
 * red   = "ROUGE"
 * blue  = "BLEU"
 * </pre>
 *
 * <code>
 * define('DIGIRENT_RESOURCEBUNDLE_INCLUDE_PATH', '/path/to/dir:/path/to/another/dir');
 *
 * $Color =& Digirent_ResourceBundle::getBundle('Color');
 * echo $Color->getString('red'); // "RED"
 * var_dump($rb->getContents()); // array('red' => 'RED', 'blue' => 'BLUE')
 *
 * $Color =& Digirent_ResourceBundle::getBundle('Color', 'fr');
 * echo $Color->getString('red'); // "ROUGE"
 * </code>
 *
 * @package Digirent_ResourceBundle
 */
class Digirent_ResourceBundle
{
    /**
     * static
     * @access public
     * @params string name 
     * @params string locale 
     * @return object 
     */
    function & getBundle($name, $locale = '')
    {
        $path = (string) @constant('DIGIRENT_RESOURCEBUNDLE_INCLUDE_PATH');
        $dirs = explode(':', $path);

        $formats = array();
        $formats[] = array('subclass' => 'PHP'     , 'extension' => 'php');
        $formats[] = array('subclass' => 'Ini'     , 'extension' => 'ini');
        $formats[] = array('subclass' => 'Property', 'extension' => 'properties');

        if ((string) $locale === '') {
            $locale = (string) @constant('DIGIRENT_RESOURCEBUNDLE_DEFAULT_LOCALE');
        }

        $locales = array();

        if (!preg_match('/^(|_.*)$/', $locale)) {
            $locale = '_' . $locale;
        }
        $locales[] = $locale;

        if (!in_array('', $locales)) {
            $locales[] = '';
        }

        $instance = null;

        foreach ($formats as $format) {

            foreach($dirs as $dir) {

                foreach ($locales as $locale) {
                    $path = sprintf('%s%s%s.%s', $dir, $name, $locale, $format['extension']);
                    if (is_readable($path)) { break; }
                    $path = null;
                }
                if ($path === null) { continue; }

                $subclass = $format['subclass'];

                if ($subclass === 'PHP') {
                    require_once "$path";
                    $instance = new $name();
                } else {
                    require_once "Digirent/ResourceBundle/{$subclass}.php";
                    $classname = "Digirent_ResourceBundle_{$subclass}";
                    $instance = new $classname($path);
                }

                break;
            }

            if ($instance === null) { continue; }
        }

        return $instance;
    }
}

