<?php

namespace phtar\gnu;

/**
 * Description of HardlinkEntry
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
class HardlinkEntry extends \phtar\v7\HardlinkEntry implements Entry {

    /**
     * Creates a new HardlinkEntry object
     * @param string $name
     * @param \phtar\gnu\phtar\gnu\Entry $linkTo
     */
    public function __construct($name, phtar\gnu\Entry $linkTo) {
        parent::__construct($name, $linkTo);
        $this->setName($name)->setLinkname($linkTo->getName());
    }

    /**
     * Returns the last time the file was accessed as timestamp.
     * @return int
     */
    public function getATime() {
        return $this->entry->getATime();
    }

    /**
     * Returns the last time the file or the inode was changed as timestamp.
     * @return int
     */
    public function getCTime() {
        return $this->entry->getCTime();
    }

    /**
     * Returns the Device Major Number
     * @return int
     */
    public function getDevMajor() {
        return $this->entry->getDevMajor();
    }

    /**
     * Returns the Device Minor Number
     * @return int
     */
    public function getDevMinor() {
        return $this->entry->getDevMinor();
    }

    /**
     * Returns the name of the owning group
     * @return string
     */
    public function getGroupName() {
        return $this->entry->getGroupName();
    }

    /**
     * Unused!
     * @return string a string with a fixed size of 4
     */
    public function getLongnames() {
        return $this->entry->getLongnames();
    }

    /**
     * Returns the offset where this file fragment begins.
     * @return int 
     */
    public function getOffset() {
        return $this->entry->getOffset();
    }

    /**
     * Returns the file's complete size. (Check the 'M'-type)
     * @return int 
     */
    public function getRealSize() {
        return $this->entry->getRealSize();
    }

    /**
     * Returns a list of sparse fragments
     * @todo Read posible additional headers 
     * @return array
     */
    public function getSparseList() {
        return $this->entry->getSparseList();
    }

    /**
     * Returns the name of the owner
     * @return string
     */
    public function getUserName() {
        return $this->entry->getUserName();
    }

    /**
     * Returns true if the next 512 bytes are used as a sparse extension header
     * @return bool
     */
    public function isExtended() {

        return $this->entry->isExtended();
    }

}
