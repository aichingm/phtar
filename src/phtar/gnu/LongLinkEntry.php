<?php

namespace phtar\gnu;

/**
 * Description of LongLinkEntry
 *
 * @author Mario Aichigner
 */
class LongLinkEntry extends PrimitiveEntry {

    /**
     * Creates a new LongLinkEntry object
     * @param string $linkTo
     */
    public function __construct($linkTo) {
        parent::__construct();
        $this->setName("././@LongLink")->setContent($linkTo);
        $this->setMode(755);
        $size = strlen($this->getContent());
        $this->setSize($size)->setRealSize($size);
        $this->setType(Archive::ENTRY_TYPE_LONG_PATHNAME);
    }

}
