<?php

namespace phtar\posix;

/**
 * Description of DirectoryEntry
 *
 * @author Mario Aichinger <aichingm@gmail.com>
 */
class DirectoryEntry extends PrimitiveEntry {
    /**
     * Creates a new DirectoryEntry object
     * @param string $name
     */
    public function __construct($name) {
        parent::__construct();
        $this->setName($name)->setContent("");
        $this->setType(Archive::ENTRY_TYPE_DIRECTORY);
    }

}
