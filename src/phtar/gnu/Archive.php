<?php

namespace phtar\gnu;

class Archive extends \phtar\v7\Archive {

    const ENTRY_TYPE_FILE = 0;
    const ENTRY_TYPE_HARDLINK = 1;
    const ENTRY_TYPE_SOFTLINK = 2;
    const ENTRY_TYPE_CHAR_DEV_NODE = 3;
    const ENTRY_TYPE_BLOCK_DEV_NODE = 4;
    const ENTRY_TYPE_DIRECTORY = 5;
    const ENTRY_TYPE_FIFO = 6;
    const ENTRY_TYPE_FILE_GNU = 7;
    const ENTRY_TYPE_DIRECTORY_LISTING = 'D';
    const ENTRY_TYPE_LONG_LINKNAME = 'K';
    const ENTRY_TYPE_LONG_PATHNAME = 'L';
    const ENTRY_TYPE_CONTINUATION = 'M';
    const ENTRY_TYPE_IGNORE_THIS = 'N';
    const ENTRY_TYPE_SPARSE = 'S';
    const ENTRY_TYPE_TAPE_VOLUME_HEADER_NAME = 'V';
    const ENTRY_TYPES = array(
        self::ENTRY_TYPE_FILE,
        self::ENTRY_TYPE_HARDLINK,
        self::ENTRY_TYPE_SOFTLINK,
        self::ENTRY_TYPE_CHAR_DEV_NODE,
        self::ENTRY_TYPE_BLOCK_DEV_NODE,
        self::ENTRY_TYPE_DIRECTORY,
        self::ENTRY_TYPE_FIFO,
        self::ENTRY_TYPE_FILE_GNU,
        self::ENTRY_TYPE_CONTINUATION,
        self::ENTRY_TYPE_DIRECTORY_LISTING,
        self::ENTRY_TYPE_IGNORE_THIS,
        self::ENTRY_TYPE_LONG_LINKNAME,
        self::ENTRY_TYPE_LONG_PATHNAME,
        self::ENTRY_TYPE_SPARSE,
        self::ENTRY_TYPE_TAPE_VOLUME_HEADER_NAME
    );

    protected $additionalHeaders = array();

    /*
     * current file funcitons
     */

    public function getName() {
        if (isset($this->additionalHeaders[self::ENTRY_TYPE_LONG_PATHNAME])) {

            return $this->additionalHeaders[self::ENTRY_TYPE_LONG_PATHNAME];
        } else {
            $this->handle->seek($this->filePointer + 0);
            $name = strstr($this->handle->read(100), "\0", true);
            return $name;
        }
    }

    public function getType() {
        $this->handle->seek($this->filePointer + 156);
        $type = $this->handle->getc();
        if (in_array($type, self::ENTRY_TYPES)) {
            return strval($type);
        } else {
            throw new UnexpectedValueException("A valid type was expected");
        }
    }

    public function current() {


        $size = $this->getSize();
        $type = $this->getType();
        $fileOffset = $this->filePointer + 512;
        if ($type == self::ENTRY_TYPE_HARDLINK || $type == self::ENTRY_TYPE_SOFTLINK) {
            $fileOffset = $this->index[$this->getLinkname()];
            //read size diffrent record
            $size = intval($this->seekRead($fileOffset + 124, 12), 8);
            $fileOffset += 512;
        } elseif ($type == self::ENTRY_TYPE_LONG_LINKNAME || $type == self::ENTRY_TYPE_LONG_PATHNAME) {
            if ($type == self::ENTRY_TYPE_LONG_LINKNAME) {
                $this->additionalHeaders[self::ENTRY_TYPE_LONG_LINKNAME] = strstr($this->seekRead($this->filePointer + 512, $size), "\0", true);
            } elseif ($type == self::ENTRY_TYPE_LONG_PATHNAME) {
                $this->additionalHeaders[self::ENTRY_TYPE_LONG_PATHNAME] = strstr($this->seekRead($this->filePointer + 512, $size), "\0", true);
            }
            $this->next();
            return $this->current();
        }
        $this->index[$this->getName()] = $this->filePointer;
        #$this->headerHandlePrototype->setBoundaries($this->filePointer, 512);
        $this->headerHandlePrototype->setString($this->seekRead($this->filePointer, 512));
        $this->contentHandlePrototype->setBoundaries($fileOffset, $size);
        $entry = new ArchiveEntry($this->headerHandlePrototype, $this->contentHandlePrototype);
        $headers = $this->additionalHeaders;
        $entry->setAdditionalHeaders($headers);
        return $entry;
    }

    public function key() {
        $name = $this->getName();
        //key is the last called function in the life cycle so we will delete headers after this call
        $this->additionalHeaders = array();
        return $name;
    }

}
