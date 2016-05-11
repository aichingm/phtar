<?php

namespace phtar\v7;

/**
 * Description of LinuxFsEntryFactory
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
class LinuxFsEntryFactory {

    /**
     * Holds a list of already used inodes
     * @var array 
     */
    private $inodes = array();

    /**
     * Creates a new LinuxFsEntry object. Sets the linkname field if the inode is found to be used more then one time.
     * @param string $filename
     * @return \phtar\v7\LinuxFsEntry
     * @throws \UnexpectedValueException
     */
    public function create($filename) {
        if ((!is_file($filename) && !is_dir($filename)) || !is_readable($filename)) {
            throw new \UnexpectedValueException("readable file expected");
        }
        $inode = fileinode($filename);
        if (!isset($this->inodes[$inode])) {
            $this->inodes[$inode] = $filename;
        }
        if ($this->inodes[$inode] != $filename) {
            $LFE = new LinuxFsEntry($filename, $this->inodes[$inode]);
        } else {
            $LFE = new LinuxFsEntry($filename);
        }
        return $LFE;
    }

}
