<?php

namespace phtar\posix;

/**
 * Description of Archive
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
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

    /**
     * Returns the name of the current entry     
     * * @Overrides \phtar\v7\Archive::getName()
     * @return string
     */
    protected function getName() {
        $name = parent::getName();

        $this->handle->seek($this->filePointer + 345);
        $hasPrefix = ($char1 = $this->handle->read(1)) != "\0";
        $this->handle->seek($this->filePointer + 345);
        $prefix = $this->handle->read(155);

        if ($hasPrefix) {
            if (strpos($prefix, "\0") === FALSE) {
                return $prefix . "/" . $name;
            }
            return strstr($prefix, "\0", true) . "/" . $name;
        } else {
            return $name;
        }
    }

    /**
     * Returns the type of the current entry
     * @Overrides \phtar\v7\Archive::getType()
     * @return mixed
     */
    protected function getType() {
        $this->handle->seek($this->filePointer + 156);
        $type = intval($this->handle->getc(), 10);
        /* $name = $this->getName();
          if ($name{strlen($name) - 1} === "/") {
          $type = 5;
          } */
        if ($type > -1 && $type < 7) {
            return $type;
        } else {
            throw new UnexpectedValueException("A valid type was expected");
        }
    }

    /**
     * Override this function to create a new Entry object
     * @Overrides \phtar\v7\Archive::createArchiveEntry()
     * @param \phtar\utils\ReadFileFunctions $headerHandlePrototype
     * @param \phtar\utils\ReadFileFunctions $contentHandlePrototype
     * @return \phtar\posix\ArchiveEntry
     */
    protected function createArchiveEntry($headerHandlePrototype, $contentHandlePrototype) {
        return new \phtar\posix\ArchiveEntry($headerHandlePrototype, $contentHandlePrototype);
    }

}
