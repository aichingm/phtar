<?php

namespace phtar\v7;

/**
 * Description of LinuxFsEntry
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
class LinuxFsEntry implements Entry {

    /**
     * Holds the name/path of the file 
     * @var string 
     */
    protected $filename = "";

    /**
     * Holds the nasme of the file to which this file is a link. Note if this is not set to "" (empty string) getType() will return 1
     * @var string 
     */
    protected $linkname = "";

    /**
     * Creats a new LinuxFsEntry object
     * @param string $filename
     * @param string $linkname
     * @throws \UnexpectedValueException gets thrown if $filename is not a file (is_file) or if $filename is not readable (is_readable)
     */
    public function __construct($filename, $linkname = "") {
        if (!is_file($filename) || !is_readable($filename)) {
            throw new \UnexpectedValueException("readable file expected");
        }

        if (is_dir($filename) && $filename{strlen($filename) - 1} != "/") {
            $filename .= "/";
        }

        $this->filename = $filename;
        $this->linkname = $linkname;
    }

    /**
     * Copys this content to another \phtar\utils\WriteFileFunctions object. Returns the number of bytes copyed this way. 
     * @param \phtar\utils\WriteFileFunctions $destHandle 
     * @param int $bufferSize copys cunks of $bufferSize 
     * @return int 
     */
    public function copy2handle(\phtar\utils\WriteFileFunctions $destHandle, $bufferSize = 8) {
        if (is_file($this->filename)) {
            return \phtar\utils\FileHandleHelper::COPY_H2H(new \phtar\utils\FileHandleReader(fopen($this->filename, "r")), $destHandle, $bufferSize);
        } else {
            return 0;
        }
    }

    /**
     * Returns the  group id
     * @return int
     */
    public function getGroupId() {
        return filegroup($this->filename);
    }

    /**
     * Returns the linkname
     * @return string
     */
    public function getLinkname() {
        return $this->linkname;
    }

    /**
     * Returns the  last modification time
     * @return int
     */
    public function getMTime() {
        return filemtime($this->filename);
    }

    /**
     * Returns the  mode
     * @return int
     */
    public function getMode() {
        return substr(sprintf('%o', fileperms($this->filename)), -4);
    }

    /**
     * Returns the name
     * @return string
     */
    public function getName() {
        return $this->filename;
    }

    /**
     * Returns the size 
     * @return int
     */
    public function getSize() {

        if (is_file($this->filename)) {
            return filesize($this->filename);
        } else {
            return 0;
        }
    }

    /**
     * Returns the type
     * @return mixed
     */
    public function getType() {
        if (is_dir($this->filename)) {
            return Archive::ENTRY_TYPE_DIRECTORY;
        } elseif ($this->linkname !== "") {
            return Archive::ENTRY_TYPE_HARDLINK;
        } else {
            return Archive::ENTRY_TYPE_FILE;
        }
    }

    /**
     * Returns the user id
     * @return int
     */
    public function getUserId() {
        return fileowner($this->filename);
    }

}
