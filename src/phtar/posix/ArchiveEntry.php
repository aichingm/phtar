<?php

namespace phtar\posix;

class ArchiveEntry extends \phtar\v7\ArchiveEntry implements Entry {

    /*public function getName() {
        $this->handle->seek(0);
        $name = $this->handle->read(100);
        if (strpos($name, "\0") === FALSE) {
            $this->handle->seek(345);
            $prefix = $this->handle->read(155);
            if (strpos($name, "\0") === FALSE) {
                return $name . $prefix;
            }
            return $name . strstr($prefix, "\0", true);
        } else {
            return strstr($name, "\0", true);
        }
    }
    */
    
    public function getName() {
        $name = parent::getName();
        $this->handle->seek(345);
        $hasPrefix = ($char1 = $this->handle->read(1)) != "\0";
        $this->handle->seek(345);
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
     * 
     * @overrides \phtar\v7\ArchiveEntry::getType();
     * @return int
     * @throws UnexpectedValueException
     */
    public function getType() {
        $this->handle->seek(156);
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

    public function getDevMajor() {
        $this->handle->seek(329);
        return intval($this->handle->read(8), 8);
    }

    public function getDevMinor() {
        $this->handle->seek(337);
        return intval($this->handle->read(8), 8);
    }

    public function getGroupName() {
        $this->handle->seek(297);
        return strstr($this->handle->read(32), "\0", true);
    }

    public function getUserName() {
        $this->handle->seek(265);
        return strstr($this->handle->read(32), "\0", true);
    }

}
