<?php

require_once __DIR__ . '/../Autoload.php';

class ArchiveV7 implements Iterator {

    private $handle;
    private $index = array();
    private $pointer = 0;
    private $filePointer = 0;

    public function validate() {
        
    }

    public function load($fileHandle) {
        if (is_resource($fileHandle)) {
            $this->handle = $fileHandle;
        } else {
            throw new InvalidArgumentException("A file handle was expected.");
        }
    }

    public function loadFile($filename) {
        if (is_file($filename)) {
            $this->handle = fopen($filename, "r+");
        } else {
            throw new InvalidArgumentException("A file name was expected.");
        }
    }

    public function search($filename) {
        
    }

    public function buildIndex() {
        $filePointer = $this->filePointer;

        while ($this->valid()) {
            $this->next();
            $this->index[$this->getName()] = $this->filePointer;
        }

        $this->filePointer = $filePointer;
    }

    /*
     * current file funcitons
     */

    protected function getTarType() {
        fseek($this->handle, $this->filePointer + 257);
        $magic = fread($this->handle, 8);
        if ($magic === "ustar  " . "\0") {
            return 1;
        } elseif ($magic === "ustar" . "\0" . "00") {
            return 2;
        } else {
            return 0;
        }
    }

    protected function getType() {
        fseek($this->handle, $this->filePointer + 156);
        $type = fgetc($this->handle);
        $name = $this->getName();
        var_dump($name{strlen($name) - 1});
        switch ($type) {
            case '0':
            case ' ':
            case "\0":
                return "file";
            case '1':
                return "hardlink";
            case '2':
                return "softlink";
            case '5':
                return "directory";
            default:
                throw new UnexpectedValueException("A valid type was expected");
        }
    }

    protected function getSize() {
        fseek($this->handle, $this->filePointer + 124);
        return intval(fread($this->handle, 12), 8);
    }

    protected function getContent() {
        $size = $this->getSize();
        if ($size > 0) {
            fseek($this->handle, $this->filePointer + 512);
            return fread($this->handle, $size);
        }
        return null;
    }

    protected function getChecksum() {
        fseek($this->handle, $this->filePointer + 148);
        $checksum = fread($this->handle, 8);
        return intval($checksum, 8);
    }

    protected function validateChecksum() {
        fseek($this->handle, $this->filePointer + 0);
        $header = fread($this->handle, 512);

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

    protected function getUserId() {
        
    }

    protected function getGroupId() {
        
    }

    protected function getMtime() {
        
    }

    protected function getName() {
        fseek($this->handle, $this->filePointer + 0);
        return strstr(fread($this->handle, 100), "\0", true); #this is a good hack replace substr(..., strpos(..., ...))
    }

    protected function getLinkname() {
        fseek($this->handle, $this->filePointer + 157);
        return fread($this->handle, 100);
    }

    protected function seekRead($position, $length) {
        fseek($this->handle, $position);
        return fread($this->handle, $length);
    }

    /*
     * Iterator
     */

    public function current() {

        $this->index[$this->getName()] = $this->filePointer;

        $size = $this->getSize();
        $type = $this->getType();
        $fileOffset = $this->filePointer + 512;
        if ($type == "hardlink" || $type == "softlink") {
            $fileOffset = $this->index[$this->getLinkname()];
            //read size diffrent record
            $size = intval($this->seekRead($fileOffset + 124, 12), 8);
            $fileOffset += 512;
        }
        return array(
            "size" => $size,
            "type" => $type,
            "checksum" => $this->getChecksum(),
            "valid_header" => $this->validateChecksum(),
            "content" => new \phtar\utils\VirtualFileCursor($this->handle, $fileOffset, $size)
        );
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
            fseek($this->handle, $this->filePointer + $i);
            if (fread($this->handle, 8) !== "\0\0\0\0\0\0\0\0") {
                return true;
            }
        }
        return false;
    }

}

$a = new ArchiveV7();
$a->loadFile($argv[1]);
$a->buildIndex();
foreach ($a as $key => $value) {
    $value["content"]->seek(0);
    # if (strpos($key, "XYZ") !== false) {
    var_dump($key, $value, $value["content"]->read(100));
    #}
}

