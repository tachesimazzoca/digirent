<?php

/**
 * Digirent_Upload
 *
 * The wrapper for the $_FILES superglobal.
 *
 * SYNOPSIS:
 * <code>
 * $upload = new Digirent_Upload();
 *
 * var_dump($upload->getFiles()); // $_FILES
 *
 * // <input type="file" name="foo"/>
 * echo $upload->getFileTmpName('foo'); // $_FILES['foo']['tmp_name'];
 *
 * // <input type="file" name="foos[]"/>
 * for ($i = 0; $i < 3; $i) {
 *     echo $upload->getFileTmpName('foos', $i); // $_FILES['foos']['tmp_name'][$i];
 * }
 *
 * // The "move" method use the "move_uploaded_file" function. 
 * // If the destination is a directory, create a new file with a unique filename.
 * if (($filename = $upload->move('file', '/path/to/move/dir/')) !== false) {
 *     echo "move uploaded file to {$filename}.";
 * }
 * </code>
 *
 * @package Digirent_Upload
 */
class Digirent_Upload
{
    /**
     * @access private
     * @var    array
     */
    var $files;

    /**
     * @access public
     * @param  array  userfile
     */
    function Digirent_Upload($files = null)
    {
        $this->files = (is_null($files)) ? $_FILES : $files;
    }

    /**
     * @access public
     * @return array
     */
    function & getFiles()
    {
        return $this->files;
    }

    /**
     * @access public
     * @param  string
     * @return array
     */
    function & getFile($name)
    {
        $value = null;
        if (isset($this->files[$name])) {
            $value = $this->files[$name];
        }
        return $value;
    }

    /**
     * @access public
     * @param  string
     * @param  string
     */
    function setFile($name, $value)
    {
        $this->files[$name] = $value;
    }

    /**
     * @access public
     * @param  array
     */
    function setFiles($values)
    {
        $this->files = $values;
    }

    /**
     * @access public
     * @param  string
     * @param  string
     * @param  integer
     * @return mixed
     */
    function getFileParam($name, $key, $index = 0)
    {
        $value = null;
        if ($file = $this->getFile($name)) {
            if (isset($file[$key])) {
                if (!is_array($file[$key])) {
                    $file[$key] = array($file[$key]);
                }
                $value = @$file[$key][(int) $index];
            }
        }
        return $value;
    }

    /**
     * @access public
     * @param  string
     * @param  integer
     * @return string
     */
    function getFileName($name, $index = 0)
    {
        return $this->getFileParam($name, 'name', $index);
    }

    /**
     * @access public
     * @param  string
     * @param  integer
     * @return integer
     */
    function getFileSize($name, $index = 0)
    {
        return $this->getFileParam($name, 'size', $index);
    }

    /**
     * @access public
     * @param  string
     * @param  integer
     * @return string
     */
    function getFileType($name, $index = 0)
    {
        return $this->getFileParam($name, 'type', $index);
    }

    /**
     * @access public
     * @param  string
     * @param  integer
     * @return integer
     */
    function getFileError($name, $index = 0)
    {
        return $this->getFileParam($name, 'error', $index);
    }

    /**
     * @access public
     * @param  string
     * @param  integer
     * @return string
     */
    function getFileTmpName($name, $index = 0)
    {
        return $this->getFileParam($name, 'tmp_name', $index);
    }

    /**
     * @deprecated
     * @access public
     * @param  string
     * @param  integer
     * @return string
     */
    function getFilePath($name, $index = 0)
    {
        return $this->getFileTmpName($name, $index);
    }

    /**
     * @access  public
     * @param   string name
     * @param   string /path/to/(file)
     * @return  string /path/to/file or FALSE on error.
     */
    function move($name, $path)
    {
        if (!$file = $this->getFile($name)) {
            return false;
        }
        if ((int) $this->getFileError($name) !== 0) {
            return false;
        }
        if (($filepath = (string) $this->getFileTmpName($name)) === '') {
            return false;
        }

        if (substr($path, -1) === DIRECTORY_SEPARATOR) {
            $path = $path . basename(tempnam($path, ''));
        }
        if (!move_uploaded_file($filepath, $path)) {
            return false;
        }

        return $path;
    }
}

