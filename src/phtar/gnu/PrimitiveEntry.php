<?php

namespace phtar\gnu;

/**
 * Description of LinuxFileEntry
 *
 * @author mario
 */
class PrimitiveEntry extends \phtar\v7\PrimitiveEntry implements Entry {

    private $realName, $userName, $groupName, $devMajor, $devMinor, $aTime,
            $cTime, $offset, $sparseList, $isExtended, $realSize, $longNames;

    public function __construct() {
        $this->setName("PrimitiveEntry")->setContent("");
        $this->setLinkname("");
        $this->setUserId(0)->setGroupId(0);
        $this->setUserName("root")->setGroupName("wheel");
        $this->setMode(600);
        $this->setSize(0)->setRealSize(0);
        $this->setType(Archive::ENTRY_TYPE_FILE);
        $this->setDevMajor(0)->setDevMinor(0);
        $this->setMTime($time = time());
        $this->setATime($time);
        $this->setCTime($time);
        $this->setExtended(0);
        $this->setOffset(0);
        $this->setLongNames(str_repeat("\0", 4));
        $this->setSparseList(array(0 => array('offset' => 0, 'numbytes' => 0,), 1 => array('offset' => 0, 'numbytes' => 0,), 2 => array('offset' => 0, 'numbytes' => 0,), 3 => array('offset' => 0, 'numbytes' => 0,)));
    }

    public function getRealName() {
        return $this->realName;
    }

    public function getUserName() {
        return $this->userName;
    }

    public function getGroupName() {
        return $this->groupName;
    }

    public function getDevMajor() {
        return $this->devMajor;
    }

    public function getDevMinor() {
        return $this->devMinor;
    }

    public function getATime() {
        return $this->aTime;
    }

    public function getCTime() {
        return $this->cTime;
    }

    public function getOffset() {
        return $this->offset;
    }

    public function getSparseList() {
        return $this->sparseList;
    }

    public function isExtended() {
        return $this->isExtended;
    }

    public function getRealSize() {
        return $this->realSize;
    }

    public function getLongNames() {
        return $this->longNames;
    }

    public function setRealName($realName) {
        $this->realName = $realName;
        return $this;
    }

    public function setUserName($userName) {
        $this->userName = $userName;
        return $this;
    }

    public function setGroupName($groupName) {
        $this->groupName = $groupName;
        return $this;
    }

    public function setDevMajor($devMajor) {
        $this->devMajor = $devMajor;
        return $this;
    }

    public function setDevMinor($devMinor) {
        $this->devMinor = $devMinor;
        return $this;
    }

    public function setATime($aTime) {
        $this->aTime = $aTime;
        return $this;
    }

    public function setCTime($cTime) {
        $this->cTime = $cTime;
        return $this;
    }

    public function setOffset($offset) {
        $this->offset = $offset;
        return $this;
    }

    public function setSparseList($sparseList) {
        $this->sparseList = $sparseList;
        return $this;
    }

    public function setExtended($isExtended) {
        $this->isExtended = $isExtended;
        return $this;
    }

    public function setRealSize($realSize) {
        $this->realSize = $realSize;
        return $this;
    }

    public function setLongNames($longNames) {
        $this->longNames = $longNames;
        return $this;
    }

}
