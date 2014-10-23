<?php

/*
 * This file is part of: phtar
 * Copyright (C) 2014  Mario Aichinger
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

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
