<?php

namespace phtar\gnu;

/**
 * Description of PrimitiveEntry
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
class PrimitiveEntry extends \phtar\v7\PrimitiveEntry implements Entry {

    private $userName, $groupName, $devMajor, $devMinor, $aTime,
            $cTime, $offset, $sparseList, $isExtended, $realSize, $longNames;

    /**
     * Creates a new PrimitiveEntry object
     */
    public function __construct() {
        parent::__construct();
        $this->setUserName("root")->setGroupName("wheel");
        $this->setDevMajor(0)->setDevMinor(0);
        $this->setMTime($time = time());
        $this->setATime($time);
        $this->setCTime($time);
        $this->setRealSize(0);
        $this->setExtended(0);
        $this->setOffset(0);
        $this->setLongNames(str_repeat("\0", 4));
        $this->setSparseList(array(0 => array('offset' => 0, 'numbytes' => 0,), 1 => array('offset' => 0, 'numbytes' => 0,), 2 => array('offset' => 0, 'numbytes' => 0,), 3 => array('offset' => 0, 'numbytes' => 0,)));
    }

    /**
     * Returns the name of the owner
     * @return string
     */
    public function getUserName() {
        return $this->userName;
    }

    /**
     * Returns the name of the owning group
     * @return string
     */
    public function getGroupName() {
        return $this->groupName;
    }

    /**
     * Returns the Device Major Number
     * @return int
     */
    public function getDevMajor() {
        return $this->devMajor;
    }

    /**
     * Returns the Device Minor Number
     * @return int
     */
    public function getDevMinor() {
        return $this->devMinor;
    }

    /**
     * Returns the last time the file was accessed as timestamp.
     * @return int
     */
    public function getATime() {
        return $this->aTime;
    }

    /**
     * Returns the last time the file or the inode was changed as timestamp.
     * @return int
     */
    public function getCTime() {
        return $this->cTime;
    }

    /**
     * Returns the offset where this file fragment begins.
     * @return int 
     */
    public function getOffset() {
        return $this->offset;
    }

    /**
     * Returns a list of sparse fragments
     * @todo Read posible additional headers 
     * @return array
     */
    public function getSparseList() {
        return $this->sparseList;
    }

    /**
     * Returns true if the next 512 bytes are used as a sparse extension header
     * @return bool
     */
    public function isExtended() {
        return $this->isExtended;
    }

    /**
     * Returns the file's complete size. (Check the 'M'-type)
     * @return int 
     */
    public function getRealSize() {
        return $this->realSize;
    }

    /**
     * Unused!
     * @return string a string with a fixed size of 4
     */
    public function getLongNames() {
        return $this->longNames;
    }

    /**
     * Sets the user name
     * @param string $userName
     * @return \phtar\gnu\PrimitiveEntry
     */
    public function setUserName($userName) {
        $this->userName = $userName;
        return $this;
    }

    /**
     * Sets the group name
     * @param string $groupName
     * @return \phtar\gnu\PrimitiveEntry
     */
    public function setGroupName($groupName) {
        $this->groupName = $groupName;
        return $this;
    }

    /**
     * Sets the Device Major Number
     * @param int $devMajor
     * @return \phtar\gnu\PrimitiveEntry
     */
    public function setDevMajor($devMajor) {
        $this->devMajor = $devMajor;
        return $this;
    }

    /**
     * Sets the Device Minor Number
     * @param int $devMinor
     * @return \phtar\gnu\PrimitiveEntry
     */
    public function setDevMinor($devMinor) {
        $this->devMinor = $devMinor;
        return $this;
    }

    /**
     * Sets the time the file was accessed the last time
     * @param int $aTime
     * @return \phtar\gnu\PrimitiveEntry
     */
    public function setATime($aTime) {
        $this->aTime = $aTime;
        return $this;
    }

    /**
     * Sets the time the file or the inode was changed the last time
     * @param int $cTime
     * @return \phtar\gnu\PrimitiveEntry
     */
    public function setCTime($cTime) {
        $this->cTime = $cTime;
        return $this;
    }

    /**
     * Sets the offset
     * @param int $offset
     * @return \phtar\gnu\PrimitiveEntry
     */
    public function setOffset($offset) {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Sets the sparselist 
     * @param array $sparseList
     * @return \phtar\gnu\PrimitiveEntry
     */
    public function setSparseList($sparseList) {
        $this->sparseList = $sparseList;
        return $this;
    }

    /**
     * Sets the isExtended flag
     * @param boolean $isExtended
     * @return \phtar\gnu\PrimitiveEntry
     */
    public function setExtended($isExtended) {
        $this->isExtended = $isExtended;
        return $this;
    }

    /**
     * Sets the real size of the file
     * @param int $realSize
     * @return \phtar\gnu\PrimitiveEntry
     */
    public function setRealSize($realSize) {
        $this->realSize = $realSize;
        return $this;
    }

    /**
     * Sets the long names
     * @param mixed $longNames
     * @return \phtar\gnu\PrimitiveEntry
     */
    public function setLongNames($longNames) {
        $this->longNames = $longNames;
        return $this;
    }

}
