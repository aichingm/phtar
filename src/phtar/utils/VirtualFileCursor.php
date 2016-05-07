<?php

namespace phtar\utils;

class VirtualFileCursor implements ReadFileFunctions {

    private $handle;
    private $offset = 0;
    private $fileStart = 0;
    private $fileEnd = 0;
    private $eofTried = false;

    const EOF_MODE_EOF = 0;
    const EOF_MODE_LENGTH = 1;
    const EOF_MODE_TRY_READ = 2;

    #private $rawMode = false;

    function __construct(ReadFileFunctions $handle, $fileOffset, $length) {
        $this->handle = $handle;
        $this->setBoundaries($fileOffset, $length);
    }

    public function read($length) {
        $end = $this->offset + $length;
        if ($end > 0) {
            if ($end > $this->length()) {
                $length = $this->length() - $this->offset;
                $this->eofTried = true;
            }
            $string = $this->handle->read($length);
            $this->offset += $length;
            return $string;
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

    public function eof($mode = 0) {
        switch ($mode) {
            case self::EOF_MODE_LENGTH:
                return !($this->fileStart + $this->offset < $this->fileEnd);
            case self::EOF_MODE_TRY_READ:
                if ($this->getc() === false) {
                    return true;
                } else {
                    $this->seek(-1, SEEK_CUR);
                    return false;
                }
            case 0:
            default :
                return $this->eofTried;
        }
    }

    public function getc() {
        if ($this->fileStart + $this->offset < $this->fileEnd) {
            $this->offset++;
            return $this->handle->getc();
        } else {
            $this->eofTried = true;
            return false;
        }
    }

    public function gets($length = null) {
        $string = $this->handle->gets($length);
        if (strlen($string) + $this->offset > $this->length()) {
            $string = substr($string, 0, $this->length() - $this->offset);
            $this->offset = $this->length();
            $this->eofTried = true;
            if(strlen($string) === 0){
                return false;
            }
        } else {
            $this->offset += strlen($string);
        }
        return $string;
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

    public function __clone() {
        $this->handle = clone $this->handle;
    }

}
