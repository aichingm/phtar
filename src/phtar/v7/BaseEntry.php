<?php

namespace phtar\v7;

/**
 * Description of BaseEntry
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
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
        $this->setSize(strlen($content));
        $this->setMode(0600);
    }

}
