<?php

namespace phtar\posix;

/**
 * Description of BaseEntry
 *
 * @author mario
 */
class DirectoryEntry extends PrimitiveEntry {

    public function __construct($name) {
        parent::__construct();
        $this->setName($name)->setContent("");
        $this->setType(Archive::ENTRY_TYPE_DIRECTORY);
    }

}
