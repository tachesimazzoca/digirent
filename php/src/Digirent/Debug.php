<?php

/**
 * Digirent_Debug
 *
 * SYNOPSIS:
 * <code>
 * // capture var_dump() output.
 * $dump = Digirent_Debug::dump($_SERVER);
 * error_log($dump);
 *
 * // convert to another encoding.
 * echo Digirent_Debug::dump($_GET, 'UTF-8');
 *
 * // or define the output encoding as default.
 * define('DIGIRENT_DEBUG_OUTPUT_ENCODING', 'SJIS-win');
 * ....
 * </code>
 *
 * @package Digirent_Debug
 */
class Digirent_Debug
{
    /**
     * Capture var_dump() output to a string.
     *
     * @static
     * @access public
     * @param  mixed 
     * @param  string
     * @param  boolean
     * @return string
     */
    function dump($var, $encoding = null, $html = null)
    {
        ob_start();
        var_dump($var);
        $output = ob_get_contents();
        ob_clean();

        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);

        if ($html === null) {
            $html = (bool) (PHP_SAPI !== 'cli');
        }
        if ($html) {
            $output = '<pre>' . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        } else {
            $output = $output . PHP_EOL;
        }

        if ($encoding === null) {
            $encoding = defined('DIGIRENT_DEBUG_OUTPUT_ENCODING') ? DIGIRENT_DEBUG_OUTPUT_ENCODING : null;
        }
        if ($encoding !== null) {
            $output = mb_convert_encoding($output, $encoding);
        }

        return $output;
    }
}

