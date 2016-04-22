<?php

namespace phtar\gnu;

/**
 * Description of ArchiveEntry
 *
 * @author Mario Aichinger
 */
class ArchiveEntry extends \phtar\v7\ArchiveEntry implements Entry {

    private $additionalHeaders;

    /**
     * Returns a list of additional headers (ENTRY_TYPE_LONG_PATHNAME, ENTRY_TYPE_LONG_LINKNAME)
     * @return array
     */
    public function getAdditionalHeaders() {
        return $this->additionalHeaders;
    }

    /**
     * Set the additional headers (ENTRY_TYPE_LONG_PATHNAME, ENTRY_TYPE_LONG_LINKNAME)
     * @param array $additionalHeaders
     * @return \phtar\gnu\ArchiveEntry
     */
    public function setAdditionalHeaders($additionalHeaders) {
        $this->additionalHeaders = $additionalHeaders;
        return $this;
    }

    /**
     * Returns the name of the file/directory
     * @return string
     */
    public function getName() {
        if (isset($this->additionalHeaders[Archive::ENTRY_TYPE_LONG_PATHNAME])) {
            return $this->additionalHeaders[Archive::ENTRY_TYPE_LONG_PATHNAME];
        } else {
            $this->handle->seek(0);
            $name = strstr($this->handle->read(100), "\0", true);
            return $name;
        }
    }

    /**
     * Returns the name to which the header is linking
     * @return string
     */
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
     * Returns the type of the header
     * @overrides \phtar\v7\ArchiveEntry::getType()
     * @return mixed
     * @throws UnexpectedValueException
     */
    public function getType() {
        $this->handle->seek(156);
        $type = $this->handle->getc();
        if (in_array($type, Archive::ENTRY_TYPES)) {
            if (is_numeric($type)) {
                return (int) $type;
            }
            return strval($type);
        } else {
            throw new UnexpectedValueException("A valid type was expected");
        }
    }

    /**
     * Returns the name of the owner
     * @return string
     */
    public function getUserName() {
        $this->handle->seek(265);
        return strstr($this->handle->read(32), "\0", true);
    }

    /**
     * Returns the name of the owning group
     * @return string
     */
    public function getGroupName() {
        $this->handle->seek(297);
        return strstr($this->handle->read(32), "\0", true);
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
     * Returns the last time the file was accessed.
     * @return int
     */
    public function getATime() {
        $this->handle->seek(345);
        return intval($this->handle->read(32), 8);
    }

    /**
     * Returns the last time the file or the inode was changed.
     * @return int
     */
    public function getCTime() {
        $this->handle->seek(357);
        return intval($this->handle->read(32), 8);
    }

    /**
     * Returns the offset where this file fragment begins.
     * @return int 
     */
    public function getOffset() {
        $this->handle->seek(369);
        return $this->handle->read(12) == "1" ? 1 : 0;
    }

    /**
     * Unused!
     * @return string a string with a fixed size of 4
     */
    public function getLongnames() {
        $this->handle->seek(381);
        return $this->handle->read(4);
    }

    /**
     * Returns a list of sparse fragments
     * @todo Read posible additional headers 
     * @return array
     */
    public function getSparseList() {
        $list = array();
        for ($i = 0; $i < 4; $i++) {
            $this->handle->seek(385 + ($i * 24));
            $list[$i]["offset"] = intval($this->handle->read(12), 8);
            $this->handle->seek(385 + ($i * 24) + 12);
            $list[$i]["numbytes"] = intval($this->handle->read(12), 8);
        }
        return $list;
    }

    /**
     * Returns true if the next 512 bytes are used as a sparse extension header
     * @return bool
     */
    public function isExtended() {
        $this->handle->seek(482);
        return $this->handle->read(1) != "\0";
    }

    /**
     * Returns the file's complete size. (Check the 'M'-type)
     * @return int 
     */
    public function getRealSize() {
        $this->handle->seek(483);
        return intval($this->handle->read(32), 8);
    }

}
