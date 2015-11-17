<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phtar\utils;

/**
 * Description of VirtualFileCursor
 *
 * @author mario
 */
class VirtualFileCursor implements FileFunctions {

    private $handle;
    private $offset = 0;
    private $fileStart = 0;
    private $fileEnd = 0;

    function __construct($handle, $fileOffset, $length) {
        $this->handle = $handle;
        if (!is_resource($this->handle)) {
            $this->handle = null;
        }
        $this->fileStart = $fileOffset;
        if ($this->fileStart < 1) {
            $this->fileStart = 0;
        }
        $this->fileEnd = $this->fileStart + $length;
        if ($this->fileEnd < 1) {
            $this->fileEnd = 0;
        }
    }

    public function read($length) {
        $end = $this->offset + $length;
        if ($end < $this->length() && $end > 0) {
            return fread($this->handle, $length);
        }
        return false;
    }

    public function seek($offset, $whence = SEEK_SET) {
        if ($whence == SEEK_SET) {
            $newOffset = $this->fileStart + $offset;
            if ($newOffset >= $this->fileStart && $newOffset < $this->fileEnd) {
                $this->offset = $offset;
                return fseek($this->handle, $this->fileStart + $offset, SEEK_SET);
            } else {
                return null;
            }
        } else if ($whence == SEEK_CUR) {
            $newOffset = $this->fileStart + $this->offset + $offset;
            if ($newOffset >= $this->fileStart && $newOffset < $this->fileEnd) {
                $this->offset += $offset;
                return fseek($this->handle, $offset, SEEK_CUR);
            } else {
                return "false";
            }
        }
        return false;
    }

    public function char() {
        return fgetc($this->handle);
    }

    public function length() {
        return $this->fileEnd - $this->fileStart;
    }

    public function getc() {
        return fgetc($this->handle);
    }

    public function gets($length) {
        $end = $this->offset + $length;
        if ($end < $this->length() && $end > 0) {
            return fgets($this->handle, $length);
        }
        return false;
    }

}
