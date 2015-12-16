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
