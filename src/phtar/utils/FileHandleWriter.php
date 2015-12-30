<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phtar\utils;

/**
 * Description of FileHandleWriter
 *
 * @author mario
 */
class FileHandleWriter implements WriteFileFunctions {

    protected $handle;
    private $closeFd = false;

    public function __construct($handle) {
        if (!is_resource($handle)) {
            throw new \UnexpectedValueException("resource expected");
        }
        $this->handle = $handle;
    }

    public function seek($offset, $whence = SEEK_SET) {
        return fseek($this->handle, $offset, $whence);
    }

    public function write($string, $length = null) {
        if ($length) {
            return fwrite($this->handle, $string, $length);
        }
        return fwrite($this->handle, $string);
    }

    public function flush() {
        return fflush($this->handle);
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
