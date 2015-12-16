<?php

/*
 * TODO
 * write* functions should seek to file beginn.
 * implement writeFinalBlocks.
 * test add!
 * test checksum
 * test with bsdtar, tar, star, ark
 */

namespace phtar\posixUs;

class ArchiveCreator implements \Countable {

    private $entries = array();
    private $handle;
    private $currFileStart = 0;
    private $currFileEnd = 0;

    public function __construct(\phtar\utils\FileFunctions $handle) {
        $this->handle = $handle;
    }

    public function add(Entry $entry) {
        $this->entries[] = $entry;
    }
    public function addWithParentDirectories(Entry $entry) {
        $parts = array_pop(explode("/", $entry->getName()));
        $str = "";
        foreach ($parts as $part) {
            $this->add(new DirEntry($str . $part));
            $str .= $part . "/";
        }
        $this->add($entry);
    }

    public function count($mode = COUNT_NORMAL) {
        return count($this->entries, $mode);
    }

    public function write() {
        $this->currFileStart = 0;
        $this->currFileEnd = 0;
        foreach ($this->entries as $entry) {
            $size = $entry->getSize();
            $this->currFileEnd = $this->currFileStart + 512;
            if ($size > 0) {
                $nullBytes = 512 - ( $size % 512 );
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

    protected function writeName(Entry $entry) {
        if (strlen($entry->getName()) > 99) {
            throw new \phtar\utils\TarException("Entry name is too long");
        }
        $name = str_pad($entry->getName(), 100, "\0", STR_PAD_RIGHT);
        $this->seek(0);
        $this->handle->write($name);
    }

    protected function writeMode(Entry $entry) {
        $mode = str_pad($entry->getMode() . " \0", 8, '0', STR_PAD_LEFT);
        $this->seek(100);
        $this->handle->write($mode);
    }

    protected function writeUid(Entry $entry) {
        $uid = str_pad(decoct($entry->getUserId()) . " \0", 8, '0', STR_PAD_LEFT);
        $this->seek(108);
        $this->handle->write($uid);
    }

    protected function writeGid(Entry $entry) {
        $gid = str_pad(decoct($entry->getGroupId()) . " \0", 8, '0', STR_PAD_LEFT);
        $this->seek(116);
        $this->handle->write($gid);
    }

    protected function writeSize(Entry $entry) {
        $size = str_pad(decoct($entry->getSize()) . " ", 12, '0', STR_PAD_LEFT);
        $this->seek(124);
        $this->handle->write($size);
    }

    protected function writeMTime(Entry $entry) {
        $mTime = str_pad(decoct($entry->getMTime()) . " ", 12, '0', STR_PAD_LEFT);
        $this->seek(136);
        $this->handle->write($mTime);
    }

    protected function writeEmptyChecksum() {
        $emptyChecksum = str_repeat(" ", 8);
        $this->seek(148);
        $this->handle->write($emptyChecksum);
    }

    protected function writeType(Entry $entry) {
        $this->seek(156);
        $this->handle->write(strval($entry->getType()));
    }

    protected function writeLinkname(Entry $entry) {
        $linkname = str_pad($entry->getLinkname(), 100, "\0", STR_PAD_RIGHT);
        $this->seek(157);
        $this->handle->write($linkname);
    }

    protected function writeMagic() {
        $this->seek(257);
        $this->handle->write("ustar\0");
    }

    protected function writeVersion() {
        $this->seek(263);
        $this->handle->write("00");
    }

    protected function writeUserName(Entry $entry) {
        $username = str_pad($entry->getUserName(), 32, "\0", STR_PAD_RIGHT);
        $this->seek(265);
        $this->handle->write($username);
    }

    protected function writeGroupName(Entry $entry) {
        $groupname = str_pad($entry->getGroupName(), 32, "\0", STR_PAD_RIGHT);
        $this->seek(297);
        $this->handle->write($groupname);
    }

    protected function writeDevMajor(Entry $entry) {
        $devMajor = str_pad($entry->getDevMajor() . " \0", 8, "0", STR_PAD_LEFT);
        $this->seek(329);
        $this->handle->write($devMajor);
    }

    protected function writeDevMinor(Entry $entry) {
        $devMinor = str_pad($entry->getDevMinor() . " \0", 8, "0", STR_PAD_LEFT);
        $this->seek(337);
        $this->handle->write($devMinor);
    }

    protected function writePrefix(Entry $entry) {
        $prefix = str_pad($entry->getPrefix(), 155, "\0", STR_PAD_RIGHT);
        $this->seek(345);
        $this->handle->write($prefix);
    }

    protected function writePadding() {
        $padding = str_repeat("\0", 12);
        $this->seek(500);
        $this->handle->write($padding);
    }

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

    protected function writeContent(Entry $entry) {
        $this->seek(512);
        $size = $entry->copy2handle($this->handle);
        if ($size > 0) {
            $nullBytes = 512 - ($size % 512);
            $this->handle->write(str_repeat("\0", $nullBytes));
        }
    }

    protected function writeFinalBlocks() {
        $this->seek($this->currFileEnd);
        $this->handle->write(str_repeat("\0", 512));
        $this->handle->write(str_repeat("\0", 512));
    }

    private function seek($offset) {
        $this->handle->seek($this->currFileStart + $offset);
    }

}
