<?php

namespace phtar\v7;

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
        if ($name{strlen($name) - 1} != "/") {
            $name .= "/";
        }
        $this->setName($name)->setContent("");
        $this->setSize(0);
        $this->setType(Archive::ENTRY_TYPE_DIRECTORY);
    }

}
