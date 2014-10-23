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
