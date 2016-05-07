<?php

namespace phtar\v7;

/**
 * Description of LinuxFsEntry
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
class LinuxFsEntry extends PrimitiveEntry {

    /**
     * Holds the name/path of the file 
     * @var string 
     */
    protected $filename = "";

    /**
     * Creats a new LinuxFsEntry object
     * @param string $filename
     * @param string $linkname
     * @throws \UnexpectedValueException gets thrown if $filename is not a file (is_file) or if $filename is not readable (is_readable)
     */
    public function __construct($filename, $linkname = "") {
        parent::__construct();

        if (!(is_file($filename) || is_dir($filename)) || !is_readable($filename)) {
            throw new \UnexpectedValueException("readable file expected");
        }

        if (is_dir($filename) && $filename{strlen($filename) - 1} != "/") {
            $filename .= "/";
        }

        $this->filename = $filename;
        $this->setName($filename);
        $this->setType($this->investigateType());
        $this->setSize(0);
        $this->setMode(substr(sprintf('%o', fileperms($this->filename)), -4));
        $this->setLinkname($linkname);
        $this->setMTime(filemtime($this->filename));
        $this->setUserId(fileowner($this->filename));
        $this->setGroupId(filegroup($this->filename));
    }

    /**
     * Returns the type
     * @return mixed
     */
    protected function investigateType() {
        if (is_dir($this->filename)) {
            return Archive::ENTRY_TYPE_DIRECTORY;
        } elseif ($this->getLinkname() !== "") {
            return Archive::ENTRY_TYPE_HARDLINK;
        } else {
            return Archive::ENTRY_TYPE_FILE;
        }
    }

    /**
     * Returns the size of the file
     * @return int
     */
    protected function investigateFileSize() {
        if (is_file($this->filename)) {
            return filesize($this->filename);
        } else {
            return 0;
        }
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

}
