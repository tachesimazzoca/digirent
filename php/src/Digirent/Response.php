<?php

/**
 * Digirent_Response
 *
 * SYNOPSIS:
 * <code>
 * // display text/html
 * $response = new Digirent_Response();
 * $response->setHeader('Content-Type', 'text/html');
 * $response->setBody('<html><body><p>Hello World!</p></body></html>');
 * $response->sendResponse();
 *
 * ...
 *
 * // output file
 * $path = '/path/to/image.gif';
 * $response = new Digirent_Response();
 * $response->setHeader('Content-Type', 'image/gif');
 * $response->setHeader('Content-Length', filesize($path));
 * $response->download($path);
 *
 * ...
 *
 * // URL redirect
 *
 * // Add the prefix to an absolute path.
 * define('DIGIRENT_RESPONSE_LOCATION_BASE', '/subdir/');
 *
 * // Add the query to the Location: URI.
 * define('DIGIRENT_RESPONSE_LOCATION_QUERY', 'guid=on');
 *
 * // Add the session ID query to the Location: URI.
 * define('DIGIRENT_RESPONSE_USE_TRANS_SID', true);
 *
 * $response = new Digirent_Response();
 * // Add the "DIGIRENT_RESPONSE_LOCATION_BASE" prefix to the absolute path.
 * $response->redirect('/absolute/path/to/file');
 * // Location: /subdir/absolute/path/to/file?guid=on
 *
 * session_start();
 * $response->redirect('/absolute/path/to/file');
 * // Location: /subdir/absolute/path/to/file?guid=on&PHPSESSID=0123456789abcdef0123456789abcdef
 *
 * // Skip the "DIGIRENT_RESPONSE_LOCATION_BASE" on the relative path.
 * $response->redirect('./relative/path/to/file');
 * // Location: ./relative/path/to/file?guid=on&PHPSESSID=0123456789abcdef0123456789abcdef
 *
 * // Disable all options.
 * $response->redirect('http://example.net/', false);
 * // http://example.net/
 * </code>
 *
 * @package Digirent_Response
 */
class Digirent_Response
{
    /**
     * @access private
     * @var    array
     */
    var $headers = array();

    /**
     * @access private
     * @var    array
     */
    var $body = array();

    /**
     * @access private
     * @var    boolean
     */
    var $finished = false;

    /**
     * @access public
     */
    function Digirent_Response()
    {
    }

    /**
     * @access public
     */
    function removeHeaders()
    {
        $this->headers = array();
    }

    /**
     * @access public
     * @param  string
     */
    function removeHeader($name = null)
    {
        if ($name === null) {
            $this->headers = array();
        } else {
            $headers = array();
            foreach ($this->headers as $header) {
                @list($key, $value) = explode(':', $header);
                $key = trim($key);
                if ((string) $name !== (string) $key) {
                    $headers[] = $header;
                }
            }
            $this->headers = $headers;
        }
    }

    /**
     * @access public
     * @param  string
     * @param  string
     */
    function setHeader($name, $value = null)
    {
        $this->headers[] = ($value === null) ? $name : $name . ': ' . $value;
    }

    /**
     * @access public
     * @return string
     */
    function getBody()
    {
        return implode('', $this->body);
    }

    /**
     * @access public
     * @param  string
     */
    function setBody($content)
    {
        $this->body = array();
        $this->appendBody($content);
    }

    /**
     * @access public
     * @param  string
     */
    function appendBody($content)
    {
        $contents = (array) $content;

        foreach ($contents as $body) {
            $this->body[] = $body;
        }
    }

    /**
     * @access public
     * @return boolean
     */
    function isFinished()
    {
        return (bool) $this->finished;
    }

    /**
     * @access public
     * @param  boolean
     */
    function setFinished($value)
    {
        $this->finished = (bool) $value;
    }

    /**
     * @access public
     */
    function sendHeaders()
    {
        foreach ($this->headers as $header) {
            header($header);
        }
    }

    /**
     * @access public
     */
    function sendResponse()
    {
        $this->setHeader('Content-Length', strlen($this->getBody()));

        $this->sendHeaders();

        echo $this->getBody();

        $this->setFinished(true);
    }

    /**
     * @access public
     */
    function setNoCache()
    {
        $this->setHeader('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
        $this->setHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
        // HTTP/1.1
        $this->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        // HTTP/1.0
        $this->setHeader('Pragma', 'no-cache');
    }

    /**
     * Send Location: header
     *
     * @access public
     * @param  string
     * @param  boolean
     */
    function redirect($url, $filter = true)
    {
        if ($filter) {

            @list($url, $flag) = explode('#', $url);

            if (defined('DIGIRENT_RESPONSE_LOCATION_BASE')) {
                if (substr($url, 0, 1) === '/') {
                    $url = preg_replace('/\/$/', '', DIGIRENT_RESPONSE_LOCATION_BASE) . $url;
                }
            }

            if ((string) @constant('DIGIRENT_RESPONSE_LOCATION_QUERY') !== '') {
                @list($u, $query) = explode('?', $url);
                if (strpos($query, DIGIRENT_RESPONSE_LOCATION_QUERY) === false) {
                    $url .= (preg_match('/\?/', $url) ? '&' : '?') . DIGIRENT_RESPONSE_LOCATION_QUERY;
                }
            }

            if ((bool) @constant('DIGIRENT_RESPONSE_USE_TRANS_SID') && (string) session_id() !== '') {
                $url .= (preg_match('/\?/', $url) ? '&' : '?') . session_name() . '=' . session_id();
            }

            if (($flag = (string) $flag) !== '') {
                $url .= '#' . $flag;
            }
        }

        $this->removeHeaders();
        $this->setHeader('Location', $url);
        $this->sendHeaders();

        $this->setFinished(true);
    }

    /**
     * @access public
     * @param  string
     */
    function download($path)
    {
        $this->sendHeaders();

        if ($fp = fopen($path, 'rb')) {
            while (!feof($fp)) {
                echo fread($fp, 4096);
                flush();
                @ob_flush();
            }
            fclose($fp);
        }

        $this->setFinished(true);
    }
}

