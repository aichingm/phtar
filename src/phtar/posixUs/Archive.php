<?php

namespace phtar\posixUs;

class Archive extends \phtar\v7\Archive {

    const ENTRY_TYPE_FILE = 0;
    const ENTRY_TYPE_HARDLINK = 1;
    const ENTRY_TYPE_SOFTLINK = 2;
    const ENTRY_TYPE_CHAR_DEV_NODE = 3;
    const ENTRY_TYPE_BLOCK_DEV_NODE = 4;
    const ENTRY_TYPE_DIRECTORY = 5;
    const ENTRY_TYPE_FIFO = 6;

    /*
     * current file funcitons
     */

    public function getName() {
        $this->handle->seek(0);
        $name = strstr($this->handle->read(100), "\0", true);
        $this->handle->seek(345);
        $prefix = strstr($this->handle->read(155), "\0", true);
        return $name . $prefix;
    }

    public function getType() {
        $this->handle->seek($this->filePointer + 156);
        $type = intval($this->handle->getc(), 10);
        $name = $this->getName();
        if ($name{strlen($name) - 1} === "/") {
            $type = 5;
        }
        if ($type > -1 && $type < 7) {
            return $type;
        } else {
            throw new UnexpectedValueException("A valid type was expected");
        }
    }

}
