<?php

require_once 'Digirent/Storage/Abstract.php';

/**
 * Digirent_Storage_File
 *
 * SYNOPSIS:
 * <code>
 * $options = array(
 *     'directory' => '/path/to/storage/dir/',
 *     'mode'      => 0777,
 * );
 * $storage =& Digirent_Storage::factory('File', $options);
 * </code>
 *
 * @package Digirent_Storage
 */
class Digirent_Storage_File extends Digirent_Storage_Abstract
{
    /**
     * @access private
     * @var    string
     */
    var $directory;

    /**
     * @access private
     * @var    integer 
     */
    var $mode;

    /**
     * @access public
     * @param  array
     */
    function Digirent_Storage_File($options = array())
    {
        parent::Digirent_Storage_Abstract();

        $this->directory = isset($options['directory']) ? (string) $options['directory'] : '';
        $this->mode = isset($options['mode']) ? (int) $options['mode'] : null;
    }

    /**
     * @access public
     * @return string
     */
    function getDirectory()
    {
        return (string) $this->directory;
    }

    /**
     * @access public
     * @param  string
     */
    function setDirectory($value)
    {
        $this->directory = (string) $value;
    }

    /**
     * @access public
     * @return integer 
     */
    function getMode()
    {
        return $this->mode;
    }

    /**
     * @access public
     * @param  integer
     */
    function setMode($value)
    {
        $this->mode = is_numeric($value) ? (int) $value : null;
    }

    /**
     * @access public
     * @param  string
     * @return string
     */
    function & read($name)
    {
        $data = null;

        if ($fp = @fopen($this->directory . $name, 'rb')) {
            while (!feof($fp)) {
                $data .= fread($fp, 4096);
            }
            fclose($fp);
        }

        return $data;
    }

    /**
     * @access public
     * @param  string
     * @param  string
     */
    function write($name, &$data)
    {
        $path = $this->directory . $name;
        if ($fp = @fopen($path, 'w')) {
            @fwrite($fp, $data);
            @fclose($fp);
            if ($this->mode !== null) {
                @chmod($path, $this->mode);
            }
        }
    }

    /**
     * @access public
     * @param  string
     * @param  integer 
     */
    function delete($name, $lifetime = 0)
    {
        $path = $this->directory . $name;
        if ($lifetime > 0) {
            if (($stats = @stat($path)) !== false) {
                if ($stats[9] >= time() - $lifetime) {
                    return;
                }
            }
        }
        @unlink($path);
    }

    /**
     * @access public
     * @param  integer 
     */
    function destroy($lifetime = 0)
    {
        if (!$dh = opendir($this->directory)) { return; }
        while (false !== ($file = readdir($dh))) {
            $path = $this->directory . $file;
            if (!is_file($path)) { continue; }
            if (time() - filemtime($path) >= $lifetime) {
                @unlink($path);
            }
        }
    }
}

