<?php

namespace phtar\v7;

/**
 * Description of Archive
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
class PrimitiveEntry implements Entry {

    /**
     * Holds the group id
     * @var int 
     */
    private $groupId;

    /**
     * Holds the linkname
     * @var string 
     */
    private $linkname;

    /**
     * Holds the last modification timestamp
     * @var int 
     */
    private $mTime;

    /**
     * Holds the mode
     * @var int 
     */
    private $mode;

    /**
     * Holds the name of the file
     * @var string 
     */
    private $name;

    /**
     * Holds the size
     * @var int 
     */
    private $size;

    /**
     * Holds the type
     * @var mixed
     */
    private $type;

    /**
     * Holds the user id
     * @var int 
     */
    private $userId;

    /**
     * Holds the content
     * @var string 
     */
    private $content;

    /**
     * Creates a new PrimitiveEntry object
     */
    public function __construct() {
        $this->setName("PrimitiveEntry")->setContent("");
        $this->setLinkname("");
        $this->setUserId(0)->setGroupId(0);
        $this->setMode(0600);
        $this->setSize(0);
        $this->setType(Archive::ENTRY_TYPE_FILE);
        $this->setMTime(time());
    }

    /**
     * Copys this content to another \phtar\utils\WriteFileFunctions object. Returns the number of bytes copyed this way. 
     * @param \phtar\utils\WriteFileFunctions $destHandle 
     * @param int $bufferSize copys cunks of $bufferSize 
     * @return int 
     */
    public function copy2handle(\phtar\utils\WriteFileFunctions $destHandle, $bufferSize = null) {
        return $destHandle->write($this->content);
    }

    /**
     * Returns the group id
     * @return int
     */
    public function getGroupId() {
        return $this->groupId;
    }

    /**
     * Returns the linkname
     * @return string
     */
    public function getLinkname() {
        return $this->linkname;
    }

    /**
     * Returns the last modification timestamp
     * @return int
     */
    public function getMTime() {
        return $this->mTime;
    }

    /**
     * Returns the mode
     * @return int
     */
    public function getMode() {
        return $this->mode;
    }

    /**
     * Returns the name
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Returns the size
     * @return int
     */
    public function getSize() {
        return $this->size;
    }

    /**
     * Returns the type
     * @return mixed
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Returns the user id
     * @return int
     */
    public function getUserId() {
        return $this->userId;
    }

    /**
     * Returns the content
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * Sets the group id
     * @param int $groupId
     * @return \phtar\v7\PrimitiveEntry
     */
    public function setGroupId($groupId) {
        $this->groupId = $groupId;
        return $this;
    }

    /**
     * Sets the linkname
     * @param string $linkname
     * @return \phtar\v7\PrimitiveEntry
     */
    public function setLinkname($linkname) {
        $this->linkname = $linkname;
        return $this;
    }

    /**
     * Sets the last modification timestamp
     * @param int $mTime
     * @return \phtar\v7\PrimitiveEntry
     */
    public function setMTime($mTime) {
        $this->mTime = $mTime;
        return $this;
    }

    /**
     * Sets the mode
     * @param int $mode
     * @return \phtar\v7\PrimitiveEntry
     */
    public function setMode($mode) {
        $this->mode = $mode;
        return $this;
    }

    /**
     * Sets the name
     * @param string $name
     * @return \phtar\v7\PrimitiveEntry
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * Sets the size
     * @param int $size
     * @return \phtar\v7\PrimitiveEntry
     */
    public function setSize($size) {
        $this->size = $size;
        return $this;
    }

    /**
     * Sets the type
     * @param mixed $type
     * @return \phtar\v7\PrimitiveEntry
     */
    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    /**
     * Sets the user id
     * @param int $userId
     * @return \phtar\v7\PrimitiveEntry
     */
    public function setUserId($userId) {
        $this->userId = $userId;
        return $this;
    }

    /**
     * Sets the content
     * @param string $content
     * @return \phtar\v7\PrimitiveEntry
     */
    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

}
