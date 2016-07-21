<?php

namespace phtar\utils;

/**
 * Description of FileHandleWriter
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
class FileHandleWriter implements WriteFileFunctions {

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

    /**
     * Creates a new FileHandleWriter object
     * @param resource $handle
     * @throws \UnexpectedValueException
     */
    public function __construct($handle) {
        if (!is_resource($handle)) {
            throw new \UnexpectedValueException("resource expected");
        }
        $this->handle = $handle;
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
     * Writes data. If $length is set it will only write data this long.
     * @param string $string
     * @param int $length
     */
    public function write($string, $length = null) {
        if ($length) {
            return fwrite($this->handle, $string, $length);
        }
        return fwrite($this->handle, $string);
    }

    /**
     * Writes every thing in the buffer to the file
     */
    public function flush() {
        return fflush($this->handle);
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

    /**
     * Returns the type of access (how the stream can be accessed). See Table 1 of the fopen() reference.
     * @return string
     */
    public function getMode() {
        return stream_get_meta_data($this->handle)['mode'];
    }

}
