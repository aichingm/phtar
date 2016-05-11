<?php

namespace phtar\posix;

/**
 * Description of LinuxFsEntry
 *
 * @author Mario Aichinger <aichingm@gmail.com>
 */
class LinuxFsEntry extends \phtar\v7\LinuxFsEntry implements Entry {

    /**
     * Holds the type of the entry
     * @var mixed
     */
    protected $type;

    /**
     * Creates a new LinuxFsEntry object
     * @param string $filename
     * @param string $linkname
     */
    public function __construct($filename, $linkname = "") {
        parent::__construct($filename, $linkname);
    }

    /**
     * Returns the name
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getName() {
        $name = PrimitiveEntry::splitName($this->filename)[1];
        return $name;
    }

    /**
     * Returns the Device Major Number
     * @return string Device Major Number
     */
    public function getDevMajor() {
        if ($this->getType() == Archive::ENTRY_TYPE_BLOCK_DEV_NODE || $this->getType() === Archive::ENTRY_TYPE_CHAR_DEV_NODE) {
            return \phtar\utils\LinuxFileHelper::MAJOR_MINOR($this->filename)[0];
        }
        return 0;
    }

    /**
     * Returns the Device Minor Number
     * @return string Device Minor Number
     */
    public function getDevMinor() {
        if ($this->getType() == Archive::ENTRY_TYPE_BLOCK_DEV_NODE || $this->getType() === Archive::ENTRY_TYPE_CHAR_DEV_NODE) {
            return \phtar\utils\LinuxFileHelper::MAJOR_MINOR($this->filename)[1];
        }
        return 0;
    }

    /**
     * Returns the group name
     * @return string group name
     */
    public function getGroupName() {
        return posix_getgrgid($this->getGroupId())['name'];
    }

    /**
     * Returns the prefix
     * @return string Device Minor Number
     */
    public function getPrefix() {
        return PrimitiveEntry::splitName($this->filename)[0];
    }

    /**
     * Returns the user name
     * @return string user name
     */
    public function getUserName() {
        return posix_getpwuid($this->getUserId())['name'];
    }

    /**
     * Investigates the type of the entry
     * @return mixed
     */
    protected function investigateType() {
        $type = filetype($this->filename);
        if (parent::getType() === \phtar\v7\Archive::ENTRY_TYPE_HARDLINK) {
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

    /**
     * Returns the type of the entry (cached version of LinuxFsEntry::investigateType())
     * @return mixed
     */
    public function getType() {
        if ($this->type === null) {
            $this->type = $this->investigateType();
        }
        return $this->type;
    }

}
