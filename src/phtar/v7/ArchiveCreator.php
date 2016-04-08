<?php

/*
 * TODO
 * write* functions should seek to file beginn.
 * test add!
 * test checksum
 * test with bsdtar, tar, star, ark
 */

namespace phtar\v7;

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
        $parts = explode("/", $entry->getName());
        $last = array_pop($parts);
        if($last == ""){
            array_pop($parts);
        }
        $str = "";
        
        foreach ($parts as $part) {
            $this->add(new DirEntry($str . $part. "/"));
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

    protected function writeLinkname(Entry $entry) {
        $linkname = str_pad($entry->getLinkname(), 100, "\0", STR_PAD_RIGHT);
        $this->seek(157);
        $this->handle->write($linkname);
    }

    protected function writePadding() {
        $padding = str_repeat("\0", 255);
        $this->seek(257);
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
            $nullBytes = \phtar\utils\Math::DIFF_NEXT_MOD_0($size, 512);
            if ($nullBytes > 0) {
                $this->handle->write(str_repeat("\0", $nullBytes));
            }
        }
    }

    protected function writeFinalBlocks() {
        $this->seek(0);
        $this->handle->write(str_repeat(chr(0), 512));
        $this->handle->write(str_repeat(chr(0), 512));
    }

    private function seek($offset) {
        $this->handle->seek($this->currFileStart + $offset);
    }

}
