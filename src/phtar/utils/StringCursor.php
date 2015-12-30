<?php

namespace phtar\utils;

class StringCursor implements ReadFileFunctions {

    private $str;
    private $size;
    private $offset = 0;
    private $eofTried = false;

    const EOF_MODE_EOF = 0;
    const EOF_MODE_LENGTH = 1;
    const EOF_MODE_TRY_READ = 2;

    function __construct($string) {
        $this->setString($string);
    }

    public function read($length) {
        $end = $this->offset + $length;
        if ($end > 0) {
            if($end > $this->size){
                $length = $this->size - $this->offset;
                $this->eofTried = true;
            }
            $string = substr($this->str, $this->offset, $length);
            $this->offset += $length;
            return $string;
        }
        return false;
    }

    public function seek($offset, $whence = SEEK_SET) {
        $this->eofTried = false;
        if ($whence == SEEK_SET) {
            if ($offset >= 0 && $offset < $this->size) {
                $this->offset = $offset;
                return 0;
            } else {
                return -1;
            }
        } else if ($whence == SEEK_CUR) {
            $newOffset = $this->offset + $offset;
            if ($newOffset >= 0 && $newOffset < $this->size) {
                $this->offset += $offset;
                return 0;
            } else {
                return -1;
            }
        }
        return -1;
    }

    public function length() {
        return $this->size;
    }

    public function eof($mode = 0) {
        switch ($mode) {
            case self::EOF_MODE_LENGTH:
                return !($this->offset < $this->size);
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
        if (isset($this->str{$this->offset})) {
            return $this->str{$this->offset++};
        } else {
            $this->eofTried = true;
            return false;
        }
    }

    public function gets($length = null) {
        $nlPos = strpos($this->str, "\n", $this->offset) + 1;
        if($nlPos === false){
            $nlPos = $this->size - $this->offset;
        }
        if ($length == null || $nlPos < $length) {
            $length = $nlPos - $this->offset;
        }
        return $this->read($length);
    }

    public function setString($string) {
        $this->str = $string;
        $this->size = strlen($string);
        $this->offset = 0;
        $this->eofTried = false;
    }

}
