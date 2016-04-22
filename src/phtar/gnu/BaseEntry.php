<?php

namespace phtar\gnu;

/**
 * Description of BaseEntry
 *
 * @author Mario Aichinger
 */
class BaseEntry extends PrimitiveEntry {

    /**
     * Creates a new BaseEntry object
     * @param string $name
     * @param string $content
     */
    public function __construct($name, $content) {
        parent::__construct();
        $this->setName($name)->setContent($content);
        $size = strlen($this->getContent());
        $this->setSize($size)->setRealSize($size);
    }

}
