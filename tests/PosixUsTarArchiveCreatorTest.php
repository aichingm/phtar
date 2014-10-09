<?php

require_once '../src/Autoload.php';

class PosixUsTarArchiveCreatorTest extends PHPUnit_Framework_TestCase {

    public function __create() {
        $tac = new \phtar\TarArchiveCreator(new phtar\PosixUSTar\TarChunkFactory, new phtar\utils\ContentFixed512Factory(), new \phtar\TarArchive());
        return $tac;
    }

    /**
     * @test
     */
    public function create() {
        $this->assertInstanceOf('\phtar\TarArchiveCreator', $this->__create());
    }

    /**
     * @test
     * @depends create
     */
    public function addDir() {
        $tac = $this->__create();
        $this->assertEquals(true, $tac->add("assets/"));
    }

    /**
     * @test
     * @depends create
     */
    public function addFile() {
        $tac = $this->__create();
        $this->assertEquals(true, $tac->add("assets/.gitignore"));
        $this->assertEquals(true, $tac->add("assets/LICENSE"));
        $this->assertEquals(true, $tac->add("assets/README.md"));
        $this->assertEquals(true, $tac->add("assets/notes"));
    }

    /**
     * @test
     * @depends create
     */
    public function addTooLong() {
        $tac = $this->__create();
        try {

            $tac->add("assets/hsvfkgjsvdfkjhgvsdkjhfvgkshjdfvgkhjsdvgkhjsdfvgkjhsdvkgsdfkjhgvsdfkjhgvsdkfjhgvsdkjfhvgkjdhsfvgkjhdsfvgkjhsdfgkjhsdvfkjhgsdvfkhjgsvdfkjhgvskdfjhvgsdkfjhgvkjdsfhvgkjhdsfvgkjhdfsvgkjdhsfvgksdjfhvgkjfsdhvg.php");
        } catch (Exception $exc) {
            $this->assertInstanceOf('\phtar\utils\TarException', $exc);
        }
    }

}
