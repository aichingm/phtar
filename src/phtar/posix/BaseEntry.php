<?php

namespace phtar\posix;

/**
 * Description of BaseEntry
 *
 * @author mario
 */
class BaseEntry extends PrimitiveEntry {

    public function __construct($name, $content) {
        parent::__construct();
        $this->setName($name)->setContent($content);
        $this->setSize(strlen($content));
        $this->setType(Archive::ENTRY_TYPE_FILE);
    }

}
