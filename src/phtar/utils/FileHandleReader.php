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
    const EOF_MODE_EOF = 0;
    const EOF_MODE_LENGTH = 1;
    const EOF_MODE_TRY_READ = 2;
    

    function __construct($handle) {
        if (!is_resource($handle)) {
            throw new \UnexpectedValueException("expecting a resource");
        }
        $this->handle = $handle;
    }

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

    public function getc() {
        return fgetc($this->handle);
    }

    public function gets($length = null) {
        if ($length) {
            return fgets($this->handle, $length);
        } else {
            return fgets($this->handle);
        }
    }

    public function length($safe = true) {
        if ($safe) {
            clearstatcache();
        }
        return fstat($this->handle)['size'];
    }

    public function read($length) {
        if($length == 0){
            return "";
        }
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
