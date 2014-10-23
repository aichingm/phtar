<?php

require_once '../src/Autoload.php';

class TarReaderFactoryTest extends PHPUnit_Framework_TestCase {

    /**
     * @test
     */
    public function create() {
        $a1 = new \phtar\TarArchive();
        $a1->loadFromFile("assets/gnu.tar");
        $a2 = new \phtar\TarArchive();
        $a2->loadFromFile("assets/gnuus.tar");
        $a3 = new \phtar\TarArchive();
        $a3->loadFromFile("assets/posixustar.tar");
        $this->assertInstanceOf("\phtar\GnuOldTar\TarReader", \phtar\TarReaderFactory::getReader($a1));
        $this->assertInstanceOf("\phtar\GnuUSTar\TarReader", \phtar\TarReaderFactory::getReader($a2));
        $this->assertInstanceOf("\phtar\PosixUSTar\TarReader", \phtar\TarReaderFactory::getReader($a3));
    }

}
