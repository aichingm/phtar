<?php

namespace phtar\gnu;

/**
 * Description of Archive
 *
 * @author Mario Aichinger
 */
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

    /**
     * Holds the additional headers
     * @var array 
     */
    protected $additionalHeaders = array();

    /**
     * Holds true if the additional headers are used and ready to be unset
     * @var bool
     */
    protected $additionalHeadersDirty = false;
    protected $additionalHeadersStart = null;

    /**
     * Scanns the archive and builds an index like array(<int offset> => <string name of the file>)
     * @overrides \phtar\v7\Archive::buildIndex()
     */
    public function buildIndex() {
        if ($this->indexState != Archive::INDEX_STATE_BUILT) {
            $this->indexState = Archive::INDEX_STATE_BUILDING;
            $filePointer = $this->filePointer;
            while ($this->valid()) {
                $this->current(); #this is needed to handle the additional headers ($this->additionalHeaders, $this->additionalHeaders->dirty)
                $this->index[$this->getName()] = is_null($this->additionalHeadersStart) ? $this->filePointer : $this->additionalHeadersStart;
                $this->next();
            }
            $this->filePointer = $filePointer;
        }
        $this->indexBuilt = Archive::INDEX_STATE_BUILT;
    }

    /*
     * Current File Funcitons
     */

    /**
     * Returns the name of the current entry
     * @overrides \phtar\v7\Archive::getName() 
     * @return string
     */
    protected function getName() {
        if (isset($this->additionalHeaders[self::ENTRY_TYPE_LONG_PATHNAME])) {
            return $this->additionalHeaders[self::ENTRY_TYPE_LONG_PATHNAME];
        } else {
            return parent::getName();
        }
    }

    /**
     * Returns the name of the current entry
     * @overrides \phtar\v7\Archive::getLinkname()
     * @return string
     */
    protected function getLinkname() {
        if (isset($this->additionalHeaders[self::ENTRY_TYPE_LONG_LINKNAME])) {
            return $this->additionalHeaders[self::ENTRY_TYPE_LONG_LINKNAME];
        } else {
            return parent::getLinkname();
        }
    }

    /**
     * Returns the type of the current entry
     * @return string
     * @throws UnexpectedValueException if the type is not one of the expected ones. See: Archive::ENTRY_TYPES
     */
    protected function getType() {
        $this->handle->seek($this->filePointer + 156);
        $type = $this->handle->getc();
        if (in_array($type, self::ENTRY_TYPES)) {
            return strval($type);
        } else {
            throw new UnexpectedValueException("A valid type was expected");
        }
    }

    /**
     * Returns the current entry
     * @overrides \Iterator::current()
     * @return ArchiveEntry
     */
    public function current() {

        if ($this->additionalHeadersDirty) {
            $this->additionalHeadersDirty = false;
            $this->additionalHeaders = array();
            $this->additionalHeadersStart = null;
        }

        $size = $this->getSize();
        $type = $this->getType();
        $fileOffset = $this->filePointer + 512;
        if ($type == self::ENTRY_TYPE_HARDLINK) {
            if (isset($this->index[$name = $this->getLinkname()])) {
                $fileOffset = $this->index[$name];
                //read size diffrent record
                $size = intval($this->seekRead($fileOffset + 124, 12), 8);
                $fileOffset += 512;
            }
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
        } elseif ($type == self::ENTRY_TYPE_LONG_LINKNAME || $type == self::ENTRY_TYPE_LONG_PATHNAME) {
            if (is_null($this->additionalHeadersStart)) {
                $this->additionalHeadersStart = $this->filePointer;
            }
            if ($type == self::ENTRY_TYPE_LONG_LINKNAME) {
                $this->additionalHeaders[self::ENTRY_TYPE_LONG_LINKNAME] = strstr($this->seekRead($this->filePointer + 512, $size), "\0", true);
            } elseif ($type == self::ENTRY_TYPE_LONG_PATHNAME) {
                $this->additionalHeaders[self::ENTRY_TYPE_LONG_PATHNAME] = strstr($this->seekRead($this->filePointer + 512, $size), "\0", true);
            }
            $this->next();
            return $this->current();
        }
        $this->index[$this->getName()] = is_null($this->additionalHeadersStart) ? $this->filePointer : $this->additionalHeadersStart;
        #$this->headerHandlePrototype->setBoundaries($this->filePointer, 512);
        $this->headerHandlePrototype->setString($this->seekRead($this->filePointer, 512));
        $this->contentHandlePrototype->setBoundaries($fileOffset, $size);

        return $this->createArchiveEntry($this->headerHandlePrototype, $this->contentHandlePrototype);
    }

    /**
     * Override this function to create a new Entry object
     * @Overrides \phtar\v7\Archive::createArchiveEntry()
     * @param type $headerHandlePrototype
     * @param type $contentHandlePrototype
     * @return \phtar\posix\ArchiveEntry
     */
    protected function createArchiveEntry($headerHandlePrototype, $contentHandlePrototype) {
        $entry = new ArchiveEntry($headerHandlePrototype, $contentHandlePrototype);
        $headers = $this->additionalHeaders; #this line clons the array
        $entry->setAdditionalHeaders($headers);
        $this->additionalHeadersDirty = true;
        return $entry;
    }

    /**
     * Returns the key of the current entry which equals the name of the entry
     * @return string
     * @overrides \Iterator::key()
     */
    public function key() {
        $name = $this->getName();
        return $name;
    }

    /*
     * Find Functions
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
        $a = $this->current();
        $this->filePointer = $oldFilepointer;
        return $a;
    }

}
