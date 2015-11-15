<?php

namespace phtar\utils;

interface WriteFileFunctions {

    public function write($str);

    public function seek($offset, $whence = SEEK_SET);
}
