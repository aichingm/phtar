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
     * Holds a list of path names which sould get skiped during the directory archiving
     * @var array 
     */
    private $skip = array();

    /**
     * Creates a new DirectoryArchiver object
     * @param \phtar\v7\ArchiveCreator $archiveCreator
     * @param string $directory
     * @param array $skip
     * @throws \InvalidArgumentException if the ArchiveCreator is not of the class \phtar\v7\ArchiveCreator, \phtar\posix\ArchiveCreator or \phtar\gnu\ArchiveCreator
     */
    public function __construct(\phtar\ArchiveCreator $archiveCreator, $directory, array $skip = array()) {

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
        $this->skip = $skip;
    }

    /**
     * Set the list of path names which will be ignored while archiving the directory
     * @param array $skip
     * @return \phtar\utils\DirectoryArchiver
     */
    public function setSkip(array $skip) {
        $this->skip = $skip;
        return $this;
    }

    protected function shouldSkip($name) {
        return in_array($name, $this->skip);
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
                $name = substr($name, 0, -1);
            }
            if ($this->shouldSkip("./" . $rootDir . $name)) {
                continue;
            }

            $name = substr($name, strlen($this->directory));
            
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
