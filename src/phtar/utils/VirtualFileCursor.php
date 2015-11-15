<?php

namespace phtar\utils;

class VirtualFileCursor implements ReadFileFunctions {

    private $handle;
    private $offset = 0;
    private $fileStart = 0;
    private $fileEnd = 0;

    #private $rawMode = false;

    function __construct(ReadFileFunctions $handle, $fileOffset, $length) {
        $this->handle = $handle;
        $this->setBoundaries($fileOffset, $length);
    }

    /* public function setRaw($mode) {
      if ($this->rawMode == false && $mode) {
      $this->fileStart -= 512;
      $this->rawMode = true;
      } else if ($this->rawMode == true && !$mode) {
      $this->fileStart += 512;
      $this->rawMode = false;
      }
      } */

    public function read($length) {
        $end = $this->offset + $length;
        if ($end < $this->length() && $end > 0) {
            return $this->handle->read($length);
        }
        return false;
    }

    public function seek($offset, $whence = SEEK_SET) {
        if ($whence == SEEK_SET) {
            $newOffset = $this->fileStart + $offset;
            if ($newOffset >= $this->fileStart && $newOffset < $this->fileEnd) {
                $this->offset = $offset;
                return $this->handle->seek($this->fileStart + $offset, SEEK_SET);
            } else {
                return -1;
            }
        } else if ($whence == SEEK_CUR) {
            $newOffset = $this->fileStart + $this->offset + $offset;
            if ($newOffset >= $this->fileStart && $newOffset < $this->fileEnd) {
                $this->offset += $offset;
                return $this->handle->seek($offset, SEEK_CUR);
            } else {
                return -1;
            }
        }
        return -1;
    }

    public function length() {
        return $this->fileEnd - $this->fileStart;
    }

    public function eof() {
        return !($this->offset < $this->fileEnd);
    }

    public function getc() {
        return $this->handle->getc();
    }

    public function gets($length = null) {
        return $this->handle->gets($length);
    }

    public function setBoundaries($offset, $length) {
        $this->fileStart = $offset;
        if ($this->fileStart < 1) {
            $this->fileStart = 0;
        }
        $this->fileEnd = $this->fileStart + $length;
        if ($this->fileEnd < 1) {
            $this->fileEnd = 0;
        }
        $this->seek(0);
    }

}
