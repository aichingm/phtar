<?php

namespace phtar\utils;

/**
 * Description of FileHandleReader
 *
 * @author Mario Aichinger <aichingm@gmail.com>
 */
class FileHandleReader implements \phtar\utils\ReadFileFunctions {

    protected $handle;
    private $closeFd = false;

    function __construct($handle) {
        if (!is_resource($handle)) {
            throw new \UnexpectedValueException("expecting a resource");
        }
        $this->handle = $handle;
    }

    public function eof() {
        return feof($this->handle);
    }

    public function getc() {
        return fgetc($this->handle);
    }

    public function gets($length = null) {
        return fgets($this->handle, $length);
    }

    public function length() {
        return fstat($this->handle)['size'];
    }

    public function read($length) {
        return fread($this->handle, $length);
    }

    public function seek($offset, $whence = SEEK_SET) {
        return fseek($this->handle, $offset, $whence);
    }

    public function __clone() {
        $this->handle = FileHandleHelper::CLONE_HANDLE($this->handle);
        $this->closeFd = true;
    }

    public function __destruct() {
        if ($this->closeFd) {
            fclose($this->handle);
        }
    }

}
