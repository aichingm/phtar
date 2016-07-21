<?php

namespace phtar\posix;

/**
 * Description of ArchiveCreator
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
class ArchiveCreator implements \phtar\ArchiveCreator {

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
     * Creates a new ArchiveCreator object
     * @param \phtar\utils\FileFunctions $handle the handy to which the archive should be writen
     * @throws \phtar\utils\PhtarException if the handle was not opened with the mode r+
     */
    public function __construct(\phtar\utils\FileFunctions $handle) {
        if ($handle->getMode() != 'r+') {
            throw new \phtar\utils\PhtarException("The given handle is not opened with the mode 'r+'");
        }
        $this->handle = $handle;
    }

    /**
     * Add an Entry to the archive buffer
     * @param \phtar\posix\Entry $entry
     */
    public function add(Entry $entry) {
        $this->entries[] = $entry;
    }

    /**
     * Add an Entry to the archive buffer and add all it parent directories too
     * @param \phtar\posix\Entry $entry
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
     * Counts the entries in the buffer
     * @overrides \Countable::count()
     * @param int $mode Default: COUNT_NORMAL
     * @return int
     */
    public function count($mode = COUNT_NORMAL) {
        return count($this->entries, $mode);
    }

    /**
     * Write all entries in the buffer to the handle and finalise the archive
     */
    public function write() {
        $this->currFileStart = 0;
        $this->currFileEnd = 0;
        foreach ($this->entries as $entry) {
            $size = $entry->getSize();
            $this->currFileEnd = $this->currFileStart + 512;
            if ($size > 0) {
                $nullBytes = \phtar\utils\Math::DIFF_NEXT_MOD_0($size, 512);
                $this->currFileEnd += $nullBytes + $size;
            }
            $this->writeName($entry);
            $this->writeMode($entry);
            $this->writeUid($entry);
            $this->writeGid($entry);
            $this->writeSize($entry);
            $this->writeMTime($entry);
            $this->writeEmptyChecksum($entry);
            $this->writeType($entry);
            $this->writeLinkname($entry);
            $this->writeMagic();
            $this->writeVersion();
            $this->writeUserName($entry);
            $this->writeGroupName($entry);
            $this->writeDevMajor($entry);
            $this->writeDevMinor($entry);
            $this->writePrefix($entry);
            $this->writePadding($entry);
            $this->writeChecksum();
            $this->writeContent($entry);
            $this->currFileStart = $this->currFileEnd;
        }
        $this->writeFinalBlocks();
    }

    /**
     * Writes the name to the handle
     * @param \phtar\posix\Entry $entry
     */
    protected function writeName(Entry $entry) {
        if (strlen($entry->getName()) > 99) {
            throw new \phtar\utils\PhtarException("Entry name is too long---");
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
     * Write the empty checksum to the handle
     * @param \phtar\posix\Entry $entry
     */
    protected function writeEmptyChecksum() {
        $emptyChecksum = str_repeat(" ", 8);
        $this->seek(148);
        $this->handle->write($emptyChecksum);
    }

    /**
     * Write the type to the handle
     * @param \phtar\posix\Entry $entry
     */
    protected function writeType(Entry $entry) {
        $this->seek(156);
        $this->handle->write(strval($entry->getType()));
    }

    /**
     * Write the linkname to the handle
     * @param \phtar\posix\Entry $entry
     */
    protected function writeLinkname(Entry $entry) {
        $linkname = str_pad($entry->getLinkname(), 100, "\0", STR_PAD_RIGHT);
        $this->seek(157);
        $this->handle->write($linkname);
    }

    /**
     * Write the magic field to the handle
     * @param \phtar\posix\Entry $entry
     */
    protected function writeMagic() {
        $this->seek(257);
        $this->handle->write("ustar\0");
    }

    /**
     * Write the tar version field to the handle
     * @param \phtar\posix\Entry $entry
     */
    protected function writeVersion() {
        $this->seek(263);
        $this->handle->write("00");
    }

    /**
     * Write the user's name to the handle
     * @param \phtar\posix\Entry $entry
     */
    protected function writeUserName(Entry $entry) {
        $username = str_pad($entry->getUserName(), 32, "\0", STR_PAD_RIGHT);
        $this->seek(265);
        $this->handle->write($username);
    }

    /**
     * Write the group's name to the handle
     * @param \phtar\posix\Entry $entry
     */
    protected function writeGroupName(Entry $entry) {
        $groupname = str_pad($entry->getGroupName(), 32, "\0", STR_PAD_RIGHT);
        $this->seek(297);
        $this->handle->write($groupname);
    }

    /**
     * Write the devices major number to the handle
     * @param \phtar\posix\Entry $entry
     */
    protected function writeDevMajor(Entry $entry) {
        $devMajor = str_pad($entry->getDevMajor() . " \0", 8, "0", STR_PAD_LEFT);
        $this->seek(329);
        $this->handle->write($devMajor);
    }

    /**
     * Write the devices minor number to the handle
     * @param \phtar\posix\Entry $entry
     */
    protected function writeDevMinor(Entry $entry) {
        $devMinor = str_pad($entry->getDevMinor() . " \0", 8, "0", STR_PAD_LEFT);
        $this->seek(337);
        $this->handle->write($devMinor);
    }

    /**
     * Write the prefix to the handle
     * @param \phtar\posix\Entry $entry
     */
    protected function writePrefix(Entry $entry) {
        $prefix = str_pad($entry->getPrefix(), 155, "\0", STR_PAD_RIGHT);
        $this->seek(345);
        $this->handle->write($prefix);
    }

    /**
     * Writes the padding to the handle
     * @param \phtar\posix\Entry $entry
     */
    protected function writePadding() {
        $padding = str_repeat("\0", 12);
        $this->seek(500);
        $this->handle->write($padding);
    }

    /**
     * Calculate the checksum of the header and write it to the handle
     * @param \phtar\posix\Entry $entry
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
     * Write the content of the entry to the handle
     * @param \phtar\posix\Entry $entry
     */
    protected function writeContent(Entry $entry) {
        $this->seek(512);
        $size = $entry->copy2handle($this->handle);
        if ($size > 0) {
            $nullBytes = 512 - ($size % 512);
            $this->handle->write(str_repeat("\0", $nullBytes));
        }
    }

    /**
     * Finalise the archive by writing two empty 512 byte blocks
     */
    protected function writeFinalBlocks() {
        $this->seek(0);
        $this->handle->write(str_repeat("\0", 512));
        $this->handle->write(str_repeat("\0", 512));
    }

    /**
     * Move around in the current entry's header/file space
     * @param int $offset
     */
    private function seek($offset) {
        $this->handle->seek($this->currFileStart + $offset);
    }

}
