<?php

namespace phtar\gnu;

/**
 * Description of LinuxFsEntry
 *
 * @author mario
 */
class LinuxFsEntry extends \phtar\v7\LinuxFsEntry implements Entry {

    protected $type;

    public function __construct($filename) {
        parent::__construct($filename);
    }

    public function getName() {
        return $this->filename;
    }

    public function getDevMajor() {
        if ($this->getType() == Archive::ENTRY_TYPE_BLOCK_DEV_NODE || $this->getType() === Archive::ENTRY_TYPE_CHAR_DEV_NODE) {
            return \phtar\utils\LinuxFileHelper::MAJOR_MINOR($this->filename)[0];
        }
        return 0;
    }

    public function getDevMinor() {
        if ($this->getType() == Archive::ENTRY_TYPE_BLOCK_DEV_NODE || $this->getType() === Archive::ENTRY_TYPE_CHAR_DEV_NODE) {
            return \phtar\utils\LinuxFileHelper::MAJOR_MINOR($this->filename)[1];
        }
        return 0;
    }

    public function getGroupName() {
        return posix_getgrgid($this->getGroupId())['name'];
    }

    public function getPrefix() {
        $prefix = PrimitiveEntry::splitName($this->filename)[0];
        if (strlen($prefix) > 154) {
            throw new \InvalidArgumentException("filename is too long");
        }
        return $prefix;
    }

    public function getUserName() {
        return posix_getpwuid($this->getUserId())['name'];
    }

    public function investigateType() {
        $type = filetype($this->filename);
        if (parent::getType() === '1') {
            $type = 'hardlink';
        }
        switch ($type) {
            case 'file':
                return (string) \phtar\posixUs\Archive::ENTRY_TYPE_FILE;
            case 'hardlink':
                return (string) \phtar\posixUs\Archive::ENTRY_TYPE_HARDLINK;
            case 'link':
                return (string) \phtar\posixUs\Archive::ENTRY_TYPE_SOFTLINK;
            case 'char':
                return (string) \phtar\posixUs\Archive::ENTRY_TYPE_CHAR_DEV_NODE;
            case 'block':
                return (string) \phtar\posixUs\Archive::ENTRY_TYPE_BLOCK_DEV_NODE;
            case 'dir':
                return (string) \phtar\posixUs\Archive::ENTRY_TYPE_DIRECTORY;
        }
    }

    public function getType() {
        if ($this->type === null) {
            $this->type = $this->investigateType();
        }
        return $this->type;
    }

    public function getATime() {
        return fileatime($this->filename);
    }

    public function getCTime() {
        return filectime($this->filename);
    }

    public function getLongnames() {
        return "";
    }

    public function getOffset() {
        return 0;
    }

    public function getRealSize() {
        return $this->getSize();
    }

    public function getSparseList() {
        return "";
    }

    public function isExtended() {
        return 0;
    }

}
