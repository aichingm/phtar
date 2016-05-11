<?php

namespace phtar\gnu;

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
     * Returns the Device Major Number
     * @return int
     */
    public function getDevMajor() {
        if ($this->getType() == Archive::ENTRY_TYPE_BLOCK_DEV_NODE || $this->getType() === Archive::ENTRY_TYPE_CHAR_DEV_NODE) {
            return \phtar\utils\LinuxFileHelper::MAJOR_MINOR($this->filename)[0];
        }
        return 0;
    }

    /**
     * Returns the Device Minor Number
     * @return int
     */
    public function getDevMinor() {
        if ($this->getType() == Archive::ENTRY_TYPE_BLOCK_DEV_NODE || $this->getType() === Archive::ENTRY_TYPE_CHAR_DEV_NODE) {
            return \phtar\utils\LinuxFileHelper::MAJOR_MINOR($this->filename)[1];
        }
        return 0;
    }

    /**
     * Returns the name of the owning group
     * @return string
     */
    public function getGroupName() {
        return posix_getgrgid($this->getGroupId())['name'];
    }

    /**
     * Returns the name of the owner
     * @return string
     */
    public function getUserName() {
        return posix_getpwuid($this->getUserId())['name'];
    }

    /**
     * Investigates the type of the entry
     * @return mixed
     */
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

    /**
     * Returns the last time the file was accessed as timestamp.
     * @return int
     */
    public function getATime() {
        return fileatime($this->filename);
    }

    /**
     * Returns the last time the file or the inode was changed as timestamp.
     * @return int
     */
    public function getCTime() {
        return filectime($this->filename);
    }

    /**
     * Unused!
     * @return string a string with a fixed size of 4
     */
    public function getLongnames() {
        return str_repeat("\0", 4);
    }

    /**
     * Returns the offset where this file fragment begins.
     * @return int 
     */
    public function getOffset() {
        return 0;
    }

    /**
     * Returns the file's complete size. (Check the 'M'-type)
     * @return int 
     */
    public function getRealSize() {
        return $this->getSize();
    }

    /**
     * Returns a list of sparse fragments
     * @todo Read posible additional headers 
     * @return array
     */
    public function getSparseList() {
        return array(0 => array('offset' => 0, 'numbytes' => 0,), 1 => array('offset' => 0, 'numbytes' => 0,), 2 => array('offset' => 0, 'numbytes' => 0,), 3 => array('offset' => 0, 'numbytes' => 0,));
    }

    /**
     * Returns true if the next 512 bytes are used as a sparse extension header
     * @return bool
     */
    public function isExtended() {
        return 0;
    }

}
