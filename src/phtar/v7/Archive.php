<?php

namespace phtar\v7;

/**
 * Description of Archive
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
class Archive implements \Iterator {

    const ENTRY_TYPE_FILE = 0;
    const ENTRY_TYPE_DIRECTORY = 5;
    const ENTRY_TYPE_HARDLINK = 1;
    const ENTRY_TYPE_SOFTLINK = 2;
    const INDEX_STATE_NONE = 0;
    const INDEX_STATE_BUILDING = 1;
    const INDEX_STATE_BUILT = 2;

    /**
     *
     * @var \phtar\utils\FileHandleReader 
     */
    protected $handle;

    /**
     *
     * @var array 
     */
    protected $index = array();

    /**
     *
     * @var int
     */
    protected $indexState = 0;

    /**
     *
     * @var int
     */
    protected $pointer = 0;

    /**
     *
     * @var int
     */
    protected $filePointer = 0;

    /**
     *
     * @var \phtar\utils\StringCursor
     */
    protected $headerHandlePrototype;

    /**
     *
     * @var \phtar\utils\VirtualFileCursor
     */
    protected $contentHandlePrototype;

    /**
     * Createa a new Archive object
     * @param \phtar\utils\FileHandleReader $handle
     */
    public function __construct(\phtar\utils\FileHandleReader $handle) {
        #$this->headerHandlePrototype = new phtar\utils\VirtualFileCursor(clone $handle, 0, 0);
        $this->headerHandlePrototype = new \phtar\utils\StringCursor("");
        $this->contentHandlePrototype = new \phtar\utils\VirtualFileCursor(clone $handle, 0, 0);
        $this->handle = $handle;
    }

    /**
     * Checks if all checksums in the archive's headers are valid
     * @return boolean
     */
    public function validate() {
        $filePointer = $this->filePointer;
        $violations = array();
        $this->filePointer = 0;
        while ($this->valid()) {
            if (!$this->validateChecksum()) {
                $violations[] = $this->getName();
            }
            $this->next();
        }
        $this->filePointer = $filePointer;
        if (count($violations) > 0) {
            return $violations;
        } else {
            return true;
        }
    }

    /**
     * Scanns the archive and builds an index like array(<int offset> => <string name of the file>)
     * While building the index the property $this->indexState is set to Archive::INDEX_STATE_BUILDING, after the index is built $this->indexState is set to Archive::INDEX_STATE_BUILT
     */
    public function buildIndex() {
        if ($this->indexState != Archive::INDEX_STATE_BUILT) {
            $this->indexState = Archive::INDEX_STATE_BUILDING;
            $filePointer = $this->filePointer;
            while ($this->valid()) {
                $this->index[$this->getName()] = $this->filePointer;
                $this->next();
            }
            $this->filePointer = $filePointer;
        }
        $this->indexState = Archive::INDEX_STATE_BUILT;
    }

    /**
     * Returns a list of entry names
     * @return array
     */
    public function listEntries() {
        $this->buildIndex();
        return array_keys($this->index);
    }

    /**
     * Returns a list of entry offsets (the keys are the entry names)
     * @return arrray
     */
    public function getIndex() {
        return $this->index;
    }

    /*
     * Current File Funcitons
     */

    /**
     * Returns the name of the current entry
     * @return string
     */
    protected function getName() {
        $this->handle->seek($this->filePointer + 0);
        $name = $this->handle->read(100);
        if (strpos($name, "\0") === FALSE) {
            return $name;
        } else {
            return strstr($name, "\0", true);
        }
    }

    /**
     * Returns the size of the content of the current entry
     * @return string
     */
    protected function getSize() {
        $this->handle->seek($this->filePointer + 124);
        return intval($this->handle->read(12), 8);
    }

    /**
     * Returns the checksum of the current entry's header
     * @return string
     */
    protected function getChecksum() {
        $this->handle->seek($this->filePointer + 148);
        $checksum = $this->handle->read(8);
        return intval($checksum, 8);
    }

    /**
     * Returns the type of the current entry
     * @return mixed
     */
    protected function getType() {
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
                throw new \UnexpectedValueException("A valid type was expected. Got: [$type]");
        }
    }

    /**
     * Returns the name of the current entry
     * @return string
     */
    protected function getLinkname() {
        $this->handle->seek($this->filePointer + 157);
        $linkname = $this->handle->read(100);
        if (strpos($linkname, "\0") === FALSE) {
            return $linkname;
        } else {
            return strstr($linkname, "\0", true);
        }
    }

    /**
     * Checks if the checksum of the entry's header is correct
     * @return boolean
     */
    protected function validateChecksum() {
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

    /**
     * Seeks to a position and reads the $length from the handle 
     * @param int $position
     * @param int $length
     * @return string
     */
    protected function seekRead($position, $length) {
        $this->handle->seek($position);
        return $this->handle->read($length);
    }

    /*
     * find functions
     */

    /**
     * Searches the archive for an entry by it's name
     * Builds the index if necessary
     * @param string $name
     * @return \phtar\gnu\ArchiveEntry
     */
    public function find($name) {
        if ($this->indexState == Archive::INDEX_STATE_NONE) {
            $this->buildIndex();
        }
        if (!isset($this->index[$name])) {
            return null;
        }
        $oldFilepointer = $this->filePointer;
        $this->filePointer = $this->index[$name];
        $this->current();
        $this->filePointer = $oldFilepointer;
        return $this->createArchiveEntry(clone $this->headerHandlePrototype, clone $this->contentHandlePrototype);
    }

    /*
     * Iterator functions
     */

    /**
     * Returns the current entry
     * @overrides \Iterator::current()
     * @return ArchiveEntry
     */
    public function current() {
        if ($this->indexState == Archive::INDEX_STATE_NONE) {
            $this->index[$this->getName()] = $this->filePointer;
        }
        $size = $this->getSize();
        $type = $this->getType();
        $fileOffset = $this->filePointer + 512;
        if ($type == self::ENTRY_TYPE_HARDLINK) {
            $fileOffset = $this->index[$this->getLinkname()];
            //read size diffrent record
            $size = intval($this->seekRead($fileOffset + 124, 12), 8);
            $fileOffset += 512;
        } elseif ($type == self::ENTRY_TYPE_SOFTLINK) {
            if ($this->indexState == Archive::INDEX_STATE_NONE) {
                $this->buildIndex();
            }
            $realName = dirname($this->getName()) . "/" . $this->getLinkname();
            //symlinks may point to a file outside of the archive
            if (isset($this->index[$realName])) {
                $fileOffset = $this->index[$realName];
                //read size diffrent record
                $size = intval($this->seekRead($fileOffset + 124, 12), 8);
                $fileOffset += 512;
            }
        }
        #$this->headerHandlePrototype->setBoundaries($this->filePointer, 512);
        $this->headerHandlePrototype->setString($this->seekRead($this->filePointer, 512));
        $this->contentHandlePrototype->setBoundaries($fileOffset, $size);

        return $this->createArchiveEntry($this->headerHandlePrototype, $this->contentHandlePrototype);
    }

    /**
     * Override this function to create a new Entry object
     * @param type $headerHandlePrototype
     * @param type $contentHandlePrototype
     * @return \phtar\v7\ArchiveEntry
     */
    protected function createArchiveEntry($headerHandlePrototype, $contentHandlePrototype) {
        return new ArchiveEntry($headerHandlePrototype, $contentHandlePrototype);
    }

    /**
     * Returns the key related to current entry
     * @overrides \Iterator::key()
     * @return string
     */
    public function key() {
        return $this->getName();
    }

    /**
     * Moves to the next element of the iterator 
     * @overrides \Iterator::next()
     * @return ArchiveEntry
     */
    public function next() {
        $size = $this->getSize();
        //skip the header
        $this->filePointer += 512;
        //add the padding to a multiple of 512
        $this->filePointer += \phtar\utils\Math::NEXT_OR_CURR_MOD_0($size, 512);
        ++$this->pointer;
    }

    /**
     * Rewindes the the iterator to its beginning
     */
    public function rewind() {
        $this->pointer = 0;
        $this->filePointer = 0;
    }

    /**
     * Checks if the current element is a valid one or if the iterator hit its end
     * @return boolean
     */
    public function valid() {
        for ($i = 0; $i < 1024; $i += 8) {
            $this->handle->seek($this->filePointer + $i);
            if ($this->handle->read(8) !== "\0\0\0\0\0\0\0\0") {
                return true;
            }
        }
        $this->indexState = Archive::INDEX_STATE_BUILT;
        return false;
    }

}
