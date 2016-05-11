<?php

namespace phtar\v7;

/**
 * Description of ArchiveEntry
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
class ArchiveEntry implements Entry {

    /**
     * Holds the handle in which the entry hreader is stored
     * @var \phtar\utils\ReadFileFunctions 
     */
    protected $handle;

    /**
     * Holds the content handle
     * @var \phtar\utils\ReadFileFunctions 
     */
    protected $contentHandle;

    /**
     * Cretats a new ArchiveEntry object
     * @param \phtar\utils\ReadFileFunctions $handle
     * @param \phtar\utils\ReadFileFunctions $contentHandle
     */
    function __construct(\phtar\utils\ReadFileFunctions $handle, \phtar\utils\ReadFileFunctions $contentHandle) {
        $this->handle = $handle;
        $this->contentHandle = $contentHandle;
    }

    /**
     * Returns the header handle of the entry
     * @return \phtar\utils\ReadFileFunctions
     */
    function getHeaderHandle() {
        return $this->handle;
    }

    /**
     * Returns the name
     * @return string
     */
    public function getName() {
        $this->handle->seek(0);
        $name = $this->handle->read(100);
        if (strpos($name, "\0") === FALSE) {
            return $name;
        } else {
            return strstr($name, "\0", true);
        }
    }

    /**
     * Returns the Mode 
     * @return int
     */
    public function getMode() {
        $this->handle->seek(100);
        return intval($this->handle->read(8), 8); # 755 is already an oclta number
    }

    /**
     * Returns the user id
     * @return int
     */
    public function getUserId() {
        $this->handle->seek(108);
        return intval($this->handle->read(8), 8);
    }

    /**
     * Returns the  group id
     * @return int
     */
    public function getGroupId() {
        $this->handle->seek(116);
        return intval($this->handle->read(8), 8);
    }

    /**
     * Returns the size
     * @return int
     */
    public function getSize() {
        $this->handle->seek(124);
        return intval($this->handle->read(12), 8);
    }

    /**
     * Returns the last modification timestamp
     * @return int
     */
    public function getMTime() {
        $this->handle->seek(136);
        return intval($this->handle->read(12), 8);
    }

    /**
     * Returns the checksum
     * @return int
     */
    public function getChecksum() {
        $this->handle->seek(148);
        $checksum = $this->handle->read(8);
        return intval($checksum, 8);
    }

    /**
     * Returns the type
     * @return mixed
     */
    public function getType() {
        $this->handle->seek(156);
        $type = $this->handle->getc();
        $name = $this->getName();
        if ($name{strlen($name) - 1} === "/") {
            $type = '5';
        }
        switch ($type) {

            case '1':
                return Archive::ENTRY_TYPE_HARDLINK;
            case '2':
                return Archive::ENTRY_TYPE_SOFTLINK;
            case '5':
                return Archive::ENTRY_TYPE_DIRECTORY;
            case '0':
            case "\0":
            default:
                return Archive::ENTRY_TYPE_FILE;
        }
    }

    /**
     * Returns the linkname
     * @return string
     */
    public function getLinkname() {
        $this->handle->seek(157);
        return strstr($this->handle->read(100), "\0", true);
    }

    /**
     * Returns the contetn as one long string or null of no content is attached
     * @return string
     */
    public function getContent() {
        $size = $this->getSize();
        if ($size > 0) {
            $this->seek(0);
            return $this->read($size);
        }
        return null;
    }

    /**
     * Checks if the checksum matches the entries header
     * @return boolean
     */
    public function validateChecksum() {
        $this->handle->seek(0);
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

    /**
     * Checks if the end of the file is reached
     * @return boolean
     */
    public function eof() {
        return $this->contentHandle->eof();
    }

    /**
     * Read one char from the content
     * @return char
     */
    public function getc() {
        return $this->contentHandle->getc();
    }

    /**
     * Reads a line (\n) or a string up to the $length from the crontent
     * @param int $length
     * @return string
     */
    public function gets($length = null) {
        ($x = $this->contentHandle->gets($length));
        return $x;
    }

    /**
     * Returns the length of the content
     * @return int
     */
    public function length() {
        return $this->contentHandle->length();
    }

    /**
     * Read $length chars/bytes from the content
     * @param int $length
     * @return string
     */
    public function read($length) {
        return $this->contentHandle->read($length);
    }

    /**
     * Seek to a position ($offset) in the content
     * @param int $offset
     * @param int $whence the mode of seeking (SEEK_, SEEK_CUR, SEEK_END)
     * @return int
     */
    public function seek($offset, $whence = SEEK_SET) {
        return $this->contentHandle->seek($offset, $whence);
    }

    /**
     * Copys this content to another \phtar\utils\WriteFileFunctions object. Returns the number of bytes copyed this way. 
     * @param \phtar\utils\WriteFileFunctions $destHandle 
     * @param int $bufferSize copys cunks of $bufferSize 
     * @return int 
     */
    public function copy2handle(\phtar\utils\WriteFileFunctions $destHandle, $bufferSize = 8) {
        return \phtar\utils\FileHandleHelper::COPY_H2H($this->contentHandle, $destHandle, $bufferSize);
    }

}
