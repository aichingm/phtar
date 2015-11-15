<?php

namespace phtar\utils;

interface ReadFileFunctions {

    public function read($length);

    public function seek($offset, $whence = SEEK_SET);

    public function getc();

    public function gets($length = null);

    public function length();

    public function eof();
    
}
