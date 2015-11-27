<?php



namespace phtar\v7;

/**
 * Description of LinuxFileEntry
 *
 * @author mario
 */
class PrimitiveEntry implements Entry {

    private $groupId, $linkname, $mTime, $mode, $name, $size, $type, $userId, $content;

    public function copy2handle(\phtar\utils\WriteFileFunctions $destHandle, $bufferSize = null) {
        return $destHandle->write($this->content);
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    public function getGroupId() {
        return $this->groupId;
    }

    public function getLinkname() {
        return $this->linkname;
    }

    public function getMTime() {
        return $this->mTime;
    }

    public function getMode() {
        return $this->mode;
    }

    public function getName() {
        return $this->name;
    }

    public function getSize() {
        return $this->size;
    }

    public function getType() {
        return $this->type;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function setGroupId($groupId) {
        $this->groupId = $groupId;
        return $this;
    }

    public function setLinkname($linkname) {
        $this->linkname = $linkname;
        return $this;
    }

    public function setMTime($mTime) {
        $this->mTime = $mTime;
        return $this;
    }

    public function setMode($mode) {
        $this->mode = $mode;
        return $this;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function setSize($size) {
        $this->size = $size;
        return $this;
    }

    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
        return $this;
    }

}
