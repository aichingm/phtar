<?php

namespace phtar\posix;

/**
 * Description of ArchiveEntry
 *
 * @author Mario Aichinger <aichingm@gmail.com>
 */
class ArchiveEntry extends \phtar\v7\ArchiveEntry implements Entry {

    /**
     * Returns the name
     * @overrides \phtar\v7\ArchiveEntry::getName()
     * @return string
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
     * Returns the type
     * @overrides \phtar\v7\ArchiveEntry::getType()
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

    /**
     * Returns the Device Major Number
     * @return int
     */
    public function getDevMajor() {
        $this->handle->seek(329);
        return intval($this->handle->read(8), 8);
    }

    /**
     * Returns the Device Minor Number
     * @return int
     */
    public function getDevMinor() {
        $this->handle->seek(337);
        return intval($this->handle->read(8), 8);
    }

    /**
     * Returns the group name
     * @return int
     */
    public function getGroupName() {
        $this->handle->seek(297);
        return strstr($this->handle->read(32), "\0", true);
    }

    /**
     * Returns the user name
     * @return int
     */
    public function getUserName() {
        $this->handle->seek(265);
        return strstr($this->handle->read(32), "\0", true);
    }
    /**
     * Returns the prefix
     * @return string
     */
    public function getPrefix() {
        $this->handle->seek(345);
        $prefix = $this->handle->read(155);

        if (strpos($prefix, "\0") === FALSE) {
            return $prefix;
        }
        return strstr($prefix, "\0", true);
    }

}
