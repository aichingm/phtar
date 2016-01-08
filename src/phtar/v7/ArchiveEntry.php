<?php

namespace phtar\v7;

class ArchiveEntry implements Entry {

    protected $handle;
    protected $contentHandle;

    function __construct(\phtar\utils\ReadFileFunctions $handle, \phtar\utils\ReadFileFunctions $contentHandle) {
        $this->handle = $handle;
        $this->contentHandle = $contentHandle;
    }

    function getHeaderHandle() {
        return $this->handle;
    }

    public function getName() {
        $this->handle->seek(0);
        return strstr($this->handle->read(100), "\0", true);
    }

    public function getMode() {
        $this->handle->seek(100);
        return intval($this->handle->read(8), 8); # 755 is already an oclta number
    }

    public function getUserId() {
        $this->handle->seek(108);
        return intval($this->handle->read(8), 8);
    }

    public function getGroupId() {
        $this->handle->seek(116);
        return intval($this->handle->read(8), 8);
    }

    public function getSize() {
        $this->handle->seek(124);
        return intval($this->handle->read(12), 8);
    }

    public function getMTime() {
        $this->handle->seek(136);
        return intval($this->handle->read(12), 8);
    }

    public function getChecksum() {
        $this->handle->seek(148);
        $checksum = $this->handle->read(8);
        return intval($checksum, 8);
    }

    public function getType() {
        $this->handle->seek(156);
        $type = $this->handle->getc();
        $name = $this->getName();
        if ($name{strlen($name) - 1} === "/") {
            $type = '5';
        }
        switch ($type) {

            case '1':
                return self::ENTRY_TYPE_HARDLINK;
            case '2':
                return self::ENTRY_TYPE_SOFTLINK;
            case '5':
                return self::ENTRY_TYPE_DIRECTORY;
            case '0':
            case "\0":
            default:
                return self::ENTRY_TYPE_FILE;
        }
    }

    public function getLinkname() {
        $this->handle->seek(157);
        return strstr($this->handle->read(100), "\0", true);
    }

    public function getContent() {
        $size = $this->getSize();
        if ($size > 0) {
            $this->seek(0);
            return $this->read($size);
        }
        return null;
    }

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

    public function eof() {
        return $this->contentHandle->eof();
    }

    public function getc() {
        return $this->contentHandle->getc();
    }

    public function gets($length = null) {
        return $this->contentHandle->gets($length);
    }

    public function length() {
        return $this->contentHandle->length();
    }

    public function read($length) {
        return $this->contentHandle->read($length);
    }

    public function seek($offset, $whence = SEEK_SET) {
        return $this->contentHandle->seek($offset, $whence);
    }

    public function copy2handle(\phtar\utils\WriteFileFunctions $destHandle, $bufferSize = 8) {
        return \phtar\utils\FileHandleHelper::COPY_H2H($this->contentHandle, $destHandle, $bufferSize);
    }

}
