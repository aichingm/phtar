<?php

namespace phtar\posix;

/**
 * Description of HardlinkEntry
 *
 * @author Mario Aichinger <aichingm@gmail.com>
 */
class HardlinkEntry extends \phtar\v7\HardlinkEntry implements Entry {

    /**
     * Creates a new HardlinkEntry object
     * @param string $name
     * @param \phtar\posix\Entry $linkTo
     */
    public function __construct($name, Entry $linkTo) {
        parent::__construct($name, $linkTo);
        $this->setName($name)->setLinkname($linkTo->getName());
    }

    /**
     * Returns the Device Major Number
     * @return string Device Major Number
     */
    public function getDevMajor() {
        $this->entry->getDevMajor();
    }

    /**
     * Returns the Device Minor Number
     * @return string Device Minor Number
     */
    public function getDevMinor() {
        $this->entry->getDevMinor();
    }

    /**
     * Returns the group name
     * @return string group name
     */
    public function getGroupName() {
        $this->entry->getGroupName();
    }

    /**
     * Returns the user name
     * @return string user name
     */
    public function getUserName() {
        $this->entry->getUserName();
    }

}
