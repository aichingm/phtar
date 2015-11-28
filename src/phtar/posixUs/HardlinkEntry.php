<?php

namespace phtar\posixUs;

/**
 * Description of LinuxFileEntry
 *
 * @author mario
 */
class HardlinkEntry implements Entry {

    private $entry, $name;

    public function __construct($name, Entry $linkTo) {
        $this->entry = $linkTo;
        $this->name = $name;
    }

    public function copy2handle(\phtar\utils\WriteFileFunctions $destHandle, $bufferSize = 8) {
        return 0;
    }

    public function getGroupId() {
        return $this->entry->getGroupId();
    }

    public function getLinkname() {
        $this->entry->getName();
    }

    public function getMTime() {
        $this->entry->getMTime();
    }

    public function getMode() {
        $this->entry->getMode();
    }

    public function getName() {
        $this->getName();
    }

    public function getSize() {
        return 0;
    }

    public function getType() {
        return 1;
    }

    public function getUserId() {
        return $this->entry->getUserId();
    }

    public function getDevMajor() {
        return $this->entry->getDevMajor();
    }

    public function getDevMinor() {
        return $this->entry->getDevMinor();
    }

    public function getGroupName() {
        return $this->entry->getGroupName();
    }

    public function getPrefix() {
        return $this->entry->getPrefix();
    }

    public function getUserName() {
        return $this->entry->getUserName();
    }

}
