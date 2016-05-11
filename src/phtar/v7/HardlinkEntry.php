<?php

namespace phtar\v7;

/**
 * Description of HardlinkEntry
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
class HardlinkEntry implements Entry {

    /**
     * Holds the entry to which this entry points
     * @var Entry 
     */
    protected $entry;

    /**
     * Holds the name of this entry
     * @var string
     */
    protected $name;

    /**
     * Creates a new HardlinkEntry object
     * @param string $name
     * @param \phtar\v7\Entry $linkTo
     */
    public function __construct($name, Entry $linkTo) {
        $this->entry = $linkTo;
        $this->name = $name;
    }

    /**
     * Returns 0. Does nothing.
     * @param \phtar\utils\WriteFileFunctions $destHandle 
     * @param int $bufferSize copys cunks of $bufferSize 
     * @return int 0 Hardlink entries have no content
     */
    public function copy2handle(\phtar\utils\WriteFileFunctions $destHandle, $bufferSize = 8) {
        return 0;
    }

    /**
     * Returns the  group id
     * @return int
     */
    public function getGroupId() {
        return $this->entry->getGroupId();
    }

    /**
     * Returns the linkname
     * @return string
     */
    public function getLinkname() {
        return $this->entry->getName();
    }

    /**
     * Returns the last modification timestamp
     * @return int
     */
    public function getMTime() {
        return $this->entry->getMTime();
    }

    /**
     * Returns the Mode 
     * @return int
     */
    public function getMode() {
        return $this->entry->getMode();
    }

    /**
     * Returns the Name 
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Returns the size. The size of a Hardlink entry is always 0
     * @return int
     */
    public function getSize() {
        return 0;
    }

    /**
     * Returns the Type. The type of a Hardlink entry is always 1
     * @return int
     */
    public function getType() {
        return Archive::ENTRY_TYPE_HARDLINK;
    }

    /**
     * Returns the user id
     * @return int
     */
    public function getUserId() {
        return $this->entry->getUserId();
    }

}
