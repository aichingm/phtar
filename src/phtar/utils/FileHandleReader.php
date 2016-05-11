<?php

namespace phtar\utils;

/**
 * Description of FileHandleReader
 *
 * @author Mario Aichinger <aichingm@gmail.com>
 */
class FileHandleReader implements \phtar\utils\ReadFileFunctions {

    /**
     * Holds a seekable resource
     * @var resource 
     */
    protected $handle;

    /**
     * Hold true if the __destruct() method should close the resource (file descriptor) $this->handle
     * @var boolean 
     */
    private $closeFd = false;

    const EOF_MODE_EOF = 0;
    const EOF_MODE_LENGTH = 1;
    const EOF_MODE_TRY_READ = 2;

    /**
     * Creates a new FileHandleReader object
     * @param resource $handle
     * @throws \UnexpectedValueException
     */
    function __construct($handle) {
        if (!is_resource($handle)) {
            throw new \UnexpectedValueException("expecting a resource");
        }
        $this->handle = $handle;
    }

    /**
     * Checks if the end of the file is reached
     * @return boolean
     */
    public function eof($mode = 0) {
        switch ($mode) {
            case 1:
                return ftell($this->handle) >= $this->length();
            case 2:
                if (fgetc($this->handle) === false) {
                    return true;
                } else {
                    $this->seek(-1, SEEK_CUR);
                    return false;
                }
            case 0:
            default :
                return feof($this->handle);
        }
    }

    /**
     * Read one char from the content
     * @return char
     */
    public function getc() {
        return fgetc($this->handle);
    }

    /**
     * Reads a line (\n) or a string up to the $length from the crontent
     * @param int $length
     * @return string
     */
    public function gets($length = null) {
        if ($length) {
            return fgets($this->handle, $length);
        } else {
            return fgets($this->handle);
        }
    }

    /**
     * Returns the length of the content
     * @return int
     */
    public function length($safe = true) {
        if ($safe) {
            clearstatcache();
        }
        return fstat($this->handle)['size'];
    }

    /**
     * Read $length chars/bytes from the content
     * @param int $length
     * @return string
     */
    public function read($length) {
        if ($length == 0) {
            return "";
        }
        return fread($this->handle, $length);
    }

    /**
     * Seek to a position ($offset) in the content
     * @param int $offset
     * @param int $whence the mode of seeking (SEEK_, SEEK_CUR, SEEK_END)
     * @return int
     */
    public function seek($offset, $whence = SEEK_SET) {
        return fseek($this->handle, $offset, $whence);
    }

    /**
     * Clones the object. Needed to clone the handle.
     */
    public function __clone() {
        $this->handle = FileHandleHelper::CLONE_HANDLE($this->handle);
        $this->closeFd = true;
    }

    /**
     * Destructs the object. Needed if $this->closeFd is set to true.
     */
    public function __destruct() {
        if ($this->closeFd) {
            fclose($this->handle);
        }
    }

}
