<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phtar\utils;

/**
 * Description of File
 *
 * @author mario
 */
class File implements FileFunctions {

    private $handle;

    public function __construct($handle) {
        if (!is_resource($handle)) {
            throw new \UnexpectedValueException("Expecting resource");
        }
        $this->handle = $handle;
    }

    public function getc() {
        return fgetc($this->handle);
    }

    public function gets($length = null) {
        return fgets($this->handle, $length);
    }

    public function length() {
        return fstat($this->handle)["size"];
    }

    public function read($length) {
        return fread($this->handle, $length);
    }

    public function seek($offset, $whence = SEEK_SET) {
        return fseek($this->handle, $offset, $whence);
    }

}
