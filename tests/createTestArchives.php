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
$tac = new \phtar\TarArchiveCreator(new phtar\PosixUSTar\TarChunkFactory, new phtar\utils\ContentFixed512Factory(), new \phtar\TarArchive());
$tac->add("assets/");
$tac->add("assets/.gitignore");
$tac->add("assets/LICENSE");
$tac->add("assets/README.md");
$tac->add("assets/notes");
file_put_contents("posixustar.tar", $tac . "");
$tac = new \phtar\TarArchiveCreator(new phtar\GnuOldTar\TarChunkFactory, new phtar\utils\ContentFixed512Factory(), new \phtar\TarArchive());

$tac->add("assets/");
$tac->add("assets/.gitignore");
$tac->add("assets/LICENSE");
$tac->add("assets/README.md");
$tac->add("assets/notes");
file_put_contents("gnu.tar", $tac . "");

$tac = new \phtar\TarArchiveCreator(new phtar\GnuUSTar\TarChunkFactory, new phtar\utils\ContentFixed512Factory(), new \phtar\TarArchive());

$tac->add("assets/");
$tac->add("assets/.gitignore");
$tac->add("assets/LICENSE");
$tac->add("assets/README.md");
$tac->add("assets/notes");
file_put_contents("gnuus.tar", $tac . "");
