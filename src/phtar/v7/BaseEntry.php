<?php

namespace phtar\v7;

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
        $this->setMode(0600);
    }

}
