<?php

namespace phtar\v7;

/**
 * Description of LinuxFsEntry
 *
 * @author mario
 */
class LinuxFsEntry implements Entry {

    protected static $USED_NODES = array();
    protected $filename = "";

    public function __construct($filename) {
        if ((!is_file($filename) && !is_dir($filename)) || !is_readable($filename)) {
            throw new \UnexpectedValueException("readable file expected");
        }

        if (is_dir($filename) && $filename{strlen($filename) - 1} != "/") {
            $filename .= "/";
        }
        $inode = fileinode($filename);
        if (!isset(self::$USED_NODES[$inode])) {
            self::$USED_NODES[$inode] = $filename;
        }

        $this->filename = $filename;
    }

    public function copy2handle(\phtar\utils\WriteFileFunctions $destHandle, $bufferSize = 8) {
        if (is_file($this->filename)) {
            return \phtar\utils\FileHandleHelper::COPY_H2H(new \phtar\utils\FileHandleReader(fopen($this->filename, "r")), $destHandle, $bufferSize);
        } else {
            return 0;
        }
    }

    public function getGroupId() {
        return filegroup($this->filename);
    }

    public function getLinkname() {
        $inode = fileinode($this->filename);
        if (isset(self::$USED_NODES[$inode]) && self::$USED_NODES[$inode] != $this->filename) {
            return self::$USED_NODES[$inode];
        }
        return "";
    }

    public function getMTime() {
        return filemtime($this->filename);
    }

    public function getMode() {
        return substr(sprintf('%o', fileperms($this->filename)), -4);
    }

    public function getName() {
        if (strlen($this->filename) > 99) {
            throw new \InvalidArgumentException("file name is too long");
        }

        return $this->filename;
    }

    public function getSize() {

        if (is_file($this->filename)) {
            return filesize($this->filename);
        } else {
            return 0;
        }
    }

    public function getType() {
        $inode = fileinode($this->filename);
        return isset(self::$USED_NODES[$inode]) && self::$USED_NODES[$inode] == $this->filename ? "0" : "1";
    }

    public function getUserId() {
        return fileowner($this->filename);
    }

}
