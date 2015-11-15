<?php

namespace phtar\utils;

class StringCursor implements ReadFileFunctions {

    private $str;
    private $size;
    private $offset = 0;


    function __construct($string) {
       $this->setString($string);
    }

    public function read($length) {
        $end = $this->offset + $length;
        if ($end < $this->size && $end > 0) {
            $string = substr($this->str, $this->offset, $length);
            $this->offset += $length;
            return $string;
        }
        return false;
    }

    public function seek($offset, $whence = SEEK_SET) {
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

    public function eof() {
        return !($this->offset < $this->size);
    }

    public function getc() {
        return $this->str{$this->offset++};
    }

    public function gets($length = null) {
        
        $nlPos = strpos("\n", $this->str, $this->offset);
        if($length == null || $nlPos < $length){
            $length = $nlPos;
        }
        return $this->read($length);
    }
    public function setString($string){
        $this->str = $string;
        $this->size = strlen($string);
        $this->offset = 0;
    }

}
