<?php

namespace phtar\v7;

class Archive implements \Iterator {

    const ENTRY_TYPE_FILE = 0;
    const ENTRY_TYPE_DIRECTORY = 1;
    const ENTRY_TYPE_HARDLINK = 2;
    const ENTRY_TYPE_SOFTLINK = 3;

    protected $handle;
    protected $index = array();
    protected $indexBuilt = false;
    protected $pointer = 0;
    protected $filePointer = 0;
    protected $headerHandlePrototype;
    protected $contentHandlePrototype;

    public function __construct(\phtar\utils\FileHandleReader $handle) {
        #$this->headerHandlePrototype = new phtar\utils\VirtualFileCursor(clone $handle, 0, 0);
        $this->headerHandlePrototype = new \phtar\utils\StringCursor("");
        $this->contentHandlePrototype = new \phtar\utils\VirtualFileCursor(clone $handle, 0, 0);
        $this->handle = $handle;
    }

    public function validate() {
        $filePointer = $this->filePointer;
        $violations = array();
        $this->filePointer = 0;
        while ($this->valid()) {
            if(!$this->validateChecksum()){
                $violations[] = $this->getName();
            }
            $this->next();
        }
        $this->filePointer = $filePointer;
        if(count($violations) > 0){
            return $violations;
        }else{
            return true;
        }
    }

    public function buildIndex() {
        if (!$this->indexBuilt) {
            $filePointer = $this->filePointer;
            while ($this->valid()) {
                $this->next();
                $this->index[$this->getName()] = $this->filePointer;
            }
            $this->filePointer = $filePointer;
        }
        $this->indexBuilt = true;
    }

    /*
     * current file funcitons
     */

    public function getName() {
        $this->handle->seek($this->filePointer + 0);
        return strstr($this->handle->read(100), "\0", true);
    }

    public function getSize() {
        $this->handle->seek($this->filePointer + 124);
        return intval($this->handle->read(12), 8);
    }

    public function getChecksum() {
        $this->handle->seek($this->filePointer + 148);
        $checksum = $this->handle->read(8);
        return intval($checksum, 8);
    }

    public function getType() {
        $this->handle->seek($this->filePointer + 156);
        $type = $this->handle->getc();
        $name = $this->getName();
        if ($name{strlen($name) - 1} === "/") {
            $type = '5';
        }
        switch ($type) {
            case '0':
            case "\0":
                return self::ENTRY_TYPE_FILE;
            case '1':
                return self::ENTRY_TYPE_HARDLINK;
            case '2':
                return self::ENTRY_TYPE_SOFTLINK;
            case '5':
                return self::ENTRY_TYPE_DIRECTORY;
            default:
                throw new UnexpectedValueException("A valid type was expected");
        }
    }

    public function getLinkname() {
        $this->handle->seek($this->filePointer + 157);
        return strstr($this->handle->read(100), "\0", true);
    }

    public function validateChecksum() {
        $this->handle->seek($this->filePointer + 0);
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
        return $sum === $this->getChecksum();
    }

    /*
     * utility functions
     */

    protected function seekRead($position, $length) {
        $this->handle->seek($position);
        return $this->handle->read($length);
    }

    /*
     * Iterator functions
     */

    public function current() {

        $this->index[$this->getName()] = $this->filePointer;

        $size = $this->getSize();
        $type = $this->getType();
        $fileOffset = $this->filePointer + 512;
        if ($type == self::ENTRY_TYPE_HARDLINK) {
            $fileOffset = $this->index[$this->getLinkname()];
            //read size diffrent record
            $size = intval($this->seekRead($fileOffset + 124, 12), 8);
            $fileOffset += 512;
        }
        #$this->headerHandlePrototype->setBoundaries($this->filePointer, 512);
        $this->headerHandlePrototype->setString($this->seekRead($this->filePointer, 512));
        $this->contentHandlePrototype->setBoundaries($fileOffset, $size);

        return new ArchiveEntry($this->headerHandlePrototype, $this->contentHandlePrototype);
    }

    public function key() {
        return $this->getName();
    }

    public function next() {
        $size = $this->getSize();
        if ($size == 0) {
            $this->filePointer += 512;
        } else {
            $this->filePointer += 512 - $size % 512 + $size;
            $this->filePointer += 512;
        }
        ++$this->pointer;
    }

    public function rewind() {
        $this->pointer = 0;
        $this->filePointer = 0;
    }

    public function valid() {
        for ($i = 0; $i < 1024; $i += 8) {
            $this->handle->seek($this->filePointer + $i);
            if ($this->handle->read(8) !== "\0\0\0\0\0\0\0\0") {
                return true;
            }
        }
        $this->indexBuilt = true;
        return false;
    }

}
