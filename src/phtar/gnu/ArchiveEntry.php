<?php

namespace phtar\gnu;

class ArchiveEntry extends \phtar\v7\ArchiveEntry implements Entry {

    private $additionalHeaders;

    public function getAdditionalHeaders() {
        return $this->additionalHeaders;
    }

    public function setAdditionalHeaders($additionalHeaders) {
        $this->additionalHeaders = $additionalHeaders;
        return $this;
    }

    public function getName() {
        if (isset($this->additionalHeaders[Archive::ENTRY_TYPE_LONG_PATHNAME])) {
            return $this->additionalHeaders[Archive::ENTRY_TYPE_LONG_PATHNAME];
        } else {
            $this->handle->seek(0);
            $name = strstr($this->handle->read(100), "\0", true);
            return $name;
        }
    }

    public function getLinkname() {
        if (isset($this->additionalHeaders[Archive::ENTRY_TYPE_LONG_LINKNAME])) {
            return $this->additionalHeaders[Archive::ENTRY_TYPE_LONG_LINKNAME];
        } else {
            $this->handle->seek(157);
            $name = strstr($this->handle->read(100), "\0", true);
            return $name;
        }
    }

    /**
     * 
     * @overrides
     * @return string
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
            return strval($type);
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
        $this->seek(297);
        return strstr($this->handle->read(32), "\0", true);
    }

    protected function writeGroupName(Entry $entry) {
        $groupname = str_pad($entry->getGroupName(), 32, "\0", STR_PAD_LEFT);
        $this->seek(297);
        $this->handle->write($groupname);
    }

    public function getUserName() {
        $this->seek(265);
        return strstr($this->handle->read(32), "\0", true);
    }

    public function getATime() {
        $this->seek(265);
        return strstr($this->handle->read(32), "\0", true);
    }

    public function getCTime() {
        $this->seek(265);
        return strstr($this->handle->read(32), "\0", true);
    }

    public function getLongnames() {
        $this->seek(265);
        return strstr($this->handle->read(32), "\0", true);
    }

    public function getOffset() {
        $this->seek(265);
        return strstr($this->handle->read(32), "\0", true);
    }

    public function getRealSize() {
        $this->seek(265);
        return strstr($this->handle->read(32), "\0", true);
    }

    public function getSparseList() {
        $this->seek(265);
        return strstr($this->handle->read(32), "\0", true);
    }

    public function isExtended() {
        $this->seek(265);
        return strstr($this->handle->read(32), "\0", true);
    }

}
