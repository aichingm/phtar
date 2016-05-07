<?php

namespace phtar;

/**
 * Description of Archive
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
abstract class Archive implements \Iterator {

    /**
     * Searches the archive for an entry by it's name
     * Builds the index if necessary
     * @param string $name
     * @return \phtar\v7\ArchiveEntry
     */
    public abstract function find($name);

    /**
     * Checks if all checksums in the archive's headers are valid
     * @return boolean
     */
    public abstract function validate();

    /**
     * Scanns the archive and builds an index like array(<int offset> => <string name of the file>)
     * While building the index the property $this->indexState is set to Archive::INDEX_STATE_BUILDING, after the index is built $this->indexState is set to Archive::INDEX_STATE_BUILT
     */
    public abstract function buildIndex();

    /**
     * Returns a list of entry names
     * @return array
     */
    public abstract function listEntries();

    /**
     * Returns a list of entry offsets (the keys are the entry names)
     * @return arrray
     */
    public abstract function getIndex();

    /**
     * Opens an Archive by a given file name
     * @param string $filename
     * @return \phtar\Archive|\phtar\v7\Archive|\phtar\gnu\Archive|\phtar\posix\Archive
     */
    public static function OPEN_BY_FILE_NAME($filename) {
        $reader = new utils\FileHandleReader(fopen($filename, "r"));
        $type = utils\ArchiveType::entryType($reader);
        switch ($type) {
            case utils\ArchiveType::TYPE_POSIX_USTAR:
                return new posix\Archive($reader);
            case utils\ArchiveType::TYPE_GNU:
                return new gnu\Archive($reader);
            case utils\ArchiveType::TYPE_V7:
            default :
                return new v7\Archive($reader);
        }
    }
    /**
     * Opens an Archive by a strings contents
     * @param string $data
     * @return \phtar\Archive|\phtar\v7\Archive|\phtar\gnu\Archive|\phtar\posix\Archive
     */
    PUBLIC STATIC FUNCTION OPEN_BY_STRING($data) {
        $reader = new utils\StringCursor($data);
        $type = utils\ArchiveType::entryType($reader);
        switch ($type) {
            case utils\ArchiveType::TYPE_POSIX_USTAR:
                return new posix\Archive($reader);
            case utils\ArchiveType::TYPE_GNU:
                return new gnu\Archive($reader);
            case utils\ArchiveType::TYPE_V7:
            default :
                return new v7\Archive($reader);
        }
    }

}
