<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phtar\utils;

/**
 * Description of LinuxFile
 *
 * @author mario
 */
class FileHandle extends FileHandleReader implements FileFunctions {

    public function flush() {
        return fflush($this->handle);
    }

    public function write($string, $length = null) {
        if ($length) {
            return fwrite($this->handle, $string, $length);
        }
        return fwrite($this->handle, $string);
    }

}
