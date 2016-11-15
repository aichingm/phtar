<?php

#License

namespace phtar\utils;

/**
 * Description of DirectoryArchiver
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
class DirectoryArchiver {

    /**
     * Hold a reference to the ArchiveCreator which will write the archive
     * @var \phtar\ArchiveCreator
     */
    private $archiveCreator;

    /**
     * Holds the path to the directory
     * @var string 
     */
    private $directory;
    private $withoutRootDir = false;

    /**
     * Holds a callable which is used as filter
     * @var callable 
     */
    private $filter = array();

    /**
     * Creates a new DirectoryArchiver object
     * @param \phtar\v7\ArchiveCreator $archiveCreator
     * @param string $directory
     * @throws \InvalidArgumentException if the ArchiveCreator is not of the class \phtar\v7\ArchiveCreator, \phtar\posix\ArchiveCreator or \phtar\gnu\ArchiveCreator
     */
    public function __construct(\phtar\ArchiveCreator $archiveCreator, $directory) {

        if (
                !($archiveCreator instanceof \phtar\v7\ArchiveCreator ||
                $archiveCreator instanceof \phtar\posix\ArchiveCreator ||
                $archiveCreator instanceof \phtar\gnu\ArchiveCreator)
        ) {
            throw new \InvalidArgumentException("Parameter 0 is not a valid ArchiveCreator");
        }

        if (!is_dir($directory)) {
            throw new \InvalidArgumentException($directory . " is not a directory");
        }

        $this->archiveCreator = $archiveCreator;
        $this->directory = realpath($directory) . "/";
    }

    /**
     * Set the callable which will be used as filter. The callable should return true or false.
     * @param callable $filter
     * @return \phtar\utils\DirectoryArchiver
     */
    public function setSkip(callable $filter) {
        $this->filter = $filter;
        return $this;
    }

    protected function shouldSkip($name) {
        $f = $this->filter;
        return $f($name);
    }

    /**
     * Start the archiving process
     */
    public function archive() {
        $fsef = $this->getFsEntryFactory();
        $Directory = new \RecursiveDirectoryIterator($this->directory);
        $Iterator = new \RecursiveIteratorIterator($Directory);

        if ($this->withoutRootDir) {
            $rootDir = "";
        } else {
            $rootDir = basename($this->directory) . "/";
        }

        foreach ($Iterator as $filename => $name) {
            if (basename($name) === "..") {
                continue;
            }
            if (basename($name) == ".") {
                #for xx/yy/zz/. cut off the last dot
                $name = substr($name, 0, -1);
            }

            $name = substr($name, strlen($this->directory));

            if ($this->shouldSkip("./" . $rootDir . $name)) {
                continue;
            }

            $entry = $fsef->create($filename);
            $entry->setName("./" . $rootDir . $name);
            $this->archiveCreator->add($entry);
        }
    }

    public function withoutRootDir() {
        $this->withoutRootDir = true;
    }

    protected function getFsEntryFactory() {
        if ($this->archiveCreator instanceof \phtar\gnu\ArchiveCreator) {
            return new \phtar\gnu\LinuxFsEntryFactory();
        } elseif ($this->archiveCreator instanceof \phtar\posix\ArchiveCreator) {
            return new \phtar\posix\LinuxFsEntryFactory();
        } else {
            return new \phtar\v7\LinuxFsEntryFactory();
        }
    }

}
