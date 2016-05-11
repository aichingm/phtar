<?php

namespace phtar\v7;

/**
 * Description of ArchiveCreator
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
class ArchiveCreator implements \Countable {

    /**
     * A list of entries which will be written to the archive file
     * @var array
     */
    private $entries = array();

    /**
     * Holds a reference to the handle to which the archive will be written
     * @var \phtar\utils\WriteFileFunctions 
     */
    private $handle;

    /**
     * Internal pointer to the start of the entry which is currently written
     * @var int 
     */
    private $currFileStart = 0;

    /**
     * Internal pointer to the end of the entry which is currently written
     * @var int 
     */
    private $currFileEnd = 0;

    /**
     * Create a new ArchiveCreator object
     * @param \phtar\utils\FileFunctions $handle
     */
    public function __construct(\phtar\utils\FileFunctions $handle) {
        $this->handle = $handle;
    }

    /**
     * Add a entry to the internal list of entries
     * @param \phtar\v7\Entry $entry
     */
    public function add(Entry $entry) {
        $this->entries[] = $entry;
    }

    /**
     * Add a entry to the internal list of entries and add all its parent Directories as DirectoryEntry. / is used as directory seperator
     * @param \phtar\v7\Entry $entry
     */
    public function addWithParentDirectories(Entry $entry) {
        $parts = explode("/", $entry->getName());
        $last = array_pop($parts);
        if ($last == "") {
            array_pop($parts);
        }
        $str = "";

        foreach ($parts as $part) {
            $this->add(new DirectoryEntry($str . $part . "/"));
            $str .= $part . "/";
        }
        $this->add($entry);
    }

    /**
     * Count all entries in the internal list.
     * @Overrides \Countable::count()
     * @param int $mode
     * @return int
     */
    public function count($mode = COUNT_NORMAL) {
        return count($this->entries, $mode);
    }

    /**
     * Write all entries in the internal list to the \phtar\utils\FileHandle passed in the constructor. Note this function adds the archive-end-blocks to the \phtar\utils\FileHandle, after this the archive is finalized
     */
    public function write() {
        $this->currFileStart = 0;
        $this->currFileEnd = 0;
        foreach ($this->entries as $entry) {
            $size = $entry->getSize();
            $this->currFileEnd = $this->currFileStart + 512;
            $this->currFileEnd += \phtar\utils\Math::NEXT_OR_CURR_MOD_0($size, 512);
            $this->writeName($entry);
            $this->writeMode($entry);
            $this->writeUid($entry);
            $this->writeGid($entry);
            $this->writeSize($entry);
            $this->writeMTime($entry);
            $this->writeEmptyChecksum($entry);
            $this->writeType($entry);
            $this->writeLinkname($entry);
            $this->writePadding($entry);
            $this->writeChecksum();
            $this->writeContent($entry);
            $this->currFileStart = $this->currFileEnd;
        }
        $this->writeFinalBlocks();
    }

    /**
     * Writes the name to the handle
     * @param \phtar\gnu\Entry $entry
     */
    protected function writeName(Entry $entry) {
        if (strlen($entry->getName()) > 99) {
            throw new \phtar\utils\PhtarException("Entry name is too long");
        }
        $name = str_pad($entry->getName(), 100, "\0", STR_PAD_RIGHT);
        $this->seek(0);
        $this->handle->write($name);
    }

    /**
     * Write the mode
     * @param \phtar\v7\Entry $entry
     */
    protected function writeMode(Entry $entry) {
        $mode = str_pad(decoct($entry->getMode()) . " \0", 8, '0', STR_PAD_LEFT);
        $this->seek(100);
        $this->handle->write($mode);
    }

    /**
     * Write the user id
     * @param \phtar\v7\Entry $entry
     */
    protected function writeUid(Entry $entry) {
        $uid = str_pad(decoct($entry->getUserId()) . " \0", 8, '0', STR_PAD_LEFT);
        $this->seek(108);
        $this->handle->write($uid);
    }

    /**
     * Write the group id
     * @param \phtar\v7\Entry $entry
     */
    protected function writeGid(Entry $entry) {
        $gid = str_pad(decoct($entry->getGroupId()) . " \0", 8, '0', STR_PAD_LEFT);
        $this->seek(116);
        $this->handle->write($gid);
    }

    /**
     * Write the size
     * @param \phtar\v7\Entry $entry
     */
    protected function writeSize(Entry $entry) {
        $size = str_pad(decoct($entry->getSize()) . " ", 12, '0', STR_PAD_LEFT);
        $this->seek(124);
        $this->handle->write($size);
    }

    /**
     * Write the last modification timestamp
     * @param \phtar\v7\Entry $entry
     */
    protected function writeMTime(Entry $entry) {
        $mTime = str_pad(decoct($entry->getMTime()) . " ", 12, '0', STR_PAD_LEFT);
        $this->seek(136);
        $this->handle->write($mTime);
    }

    /**
     * Write the empty checksum as placeholder
     */
    protected function writeEmptyChecksum() {
        $emptyChecksum = str_repeat(" ", 8);
        $this->seek(148);
        $this->handle->write($emptyChecksum);
    }

    /**
     * Write the type
     * @param \phtar\v7\Entry $entry
     */
    protected function writeType(Entry $entry) {
        switch ($entry->getType()) {
            case Archive::ENTRY_TYPE_HARDLINK:
                $type = 1;
                break;
            case Archive::ENTRY_TYPE_FILE:
            default :
                $type = "\0";
                break;
        }
        $this->seek(156);
        $this->handle->write($type);
    }

    /**
     * Write the linkname
     * @param \phtar\v7\Entry $entry
     */
    protected function writeLinkname(Entry $entry) {
        $linkname = str_pad($entry->getLinkname(), 100, "\0", STR_PAD_RIGHT);
        $this->seek(157);
        $this->handle->write($linkname);
    }

    /**
     * Write the padding
     */
    protected function writePadding() {
        $padding = str_repeat("\0", 255);
        $this->seek(257);
        $this->handle->write($padding);
    }

    /**
     * Calculate and write the checksum
     */
    protected function writeChecksum() {
        $this->handle->flush();
        $this->seek(0);
        $header = $this->handle->read(512);

        for ($i = 148; $i < 156; $i++) {
            $header{$i} = " ";
        }
        $byte_array = unpack('C*', $header);
        unset($header);
        $sum = 0;
        foreach ($byte_array as $char) {
            $sum += $char;
        }
        $this->seek(148);
        $this->handle->write(str_pad(decoct($sum) . "\0" . " ", 8, "0", STR_PAD_LEFT));
    }

    /**
     * Write the content of the file puls the max-512-byte-padding
     * @param \phtar\v7\Entry $entry
     */
    protected function writeContent(Entry $entry) {
        $this->seek(512);
        $size = $entry->copy2handle($this->handle);
        if ($size > 0) {
            $nullBytes = \phtar\utils\Math::DIFF_NEXT_MOD_0($size, 512);
            if ($nullBytes > 0) {
                $this->handle->write(str_repeat("\0", $nullBytes));
            }
        }
    }

    /**
     * Write the archive-end-blocks which finalizes the archive
     */
    protected function writeFinalBlocks() {
        $this->seek(0);
        $this->handle->write(str_repeat(chr(0), 512));
        $this->handle->write(str_repeat(chr(0), 512));
    }

    /**
     * Seek to the header to a offset. Seeks relative to the beginn of current header
     * @param int $offset
     */
    private function seek($offset) {
        $this->handle->seek($this->currFileStart + $offset);
    }

}
