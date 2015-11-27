<?php

namespace phtar\posixUs;

class ArchiveEntry extends \phtar\v7\ArchiveEntry implements Entry {

    public function getName() {
        $this->handle->seek(0);
        $name = strstr($this->handle->read(100), "\0", true);
        $this->handle->seek(345);
        $prefix = strstr($this->handle->read(155), "\0", true);
        return $name . $prefix;
    }

    /**
     * 
     * @overrides
     * @return string
     * @throws UnexpectedValueException
     */
    public function getType() {
        $this->handle->seek(156);
        $type = $this->handle->getc();
        $name = $this->getName();
        if ($name{strlen($name) - 1} === "/") {
            $type = '5';
        }
        switch ($type) {
            case '0':
            case "\0":
                return self::ENTRY_TYPE_FILE;
            case '1':
                return self::ENTRY_TYPE_HARDLINK;
            case '2':
                return self::ENTRY_TYPE_SOFTLINK;
            case '5':
                return self::ENTRY_TYPE_DIRECTORY;
            default:
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
        #return posix_getpwuid($this->getUserId())['name'];
        #return posix_getgrgid($this->getGroupId())['name'];
        $this->seek(297);
        return strstr($this->handle->read(32), "\0", true);
    }

    protected function writeGroupName(Entry $entry) {
        $groupname = str_pad($entry->getGroupName(), 32, "\0", STR_PAD_LEFT);
        $this->seek(297);
        $this->handle->write($groupname);
    }

    public function getPrefix() {
        $this->handle->seek(345);
        $prefix = strstr($this->handle->read(155), "\0", true);
        return $prefix;
    }

    public function getUserName() {
        $this->seek(265);
        return strstr($this->handle->read(32), "\0", true);
    }

}