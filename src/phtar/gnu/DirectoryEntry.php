<?php

namespace phtar\gnu;

/**
 * Description of BaseEntry
 *
 * @author Mario Aichinger
 */
class DirectoryEntry extends PrimitiveEntry {

    /**
     * Creates a new DirectoryEntry object
     * @param string $name
     */
    public function __construct($name) {
        parent::__construct();
        $this->setName($name)->setContent("");
        $this->setSize(0);
        $this->setType(Archive::ENTRY_TYPE_DIRECTORY);
    }

}
