<?php

namespace phtar\gnu;

/**
 * Description of LongNameEntry
 *
 * @author Mario Aichigner
 */
class LongNameEntry extends PrimitiveEntry {

    /**
     * Creates a new LongNameEntry object
     * @param string $name
     */
    public function __construct($name) {
        parent::__construct();
        $this->setName("././@LongLink")->setContent($name);
        $this->setMode(755);
        $size = strlen($this->getContent());
        $this->setSize($size)->setRealSize($size);
        $this->setType(Archive::ENTRY_TYPE_LONG_PATHNAME);
    }

}
