<?php

namespace phtar\utils;

interface WriteFileFunctions {

    public function flush();

    public function write($string, $length = null);

    public function seek($offset, $whence = SEEK_SET);
}
