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

namespace phtar\GnuOldTar;

/**
 * A class which is able to generate gnu old tar meta blocks in the length of 
 * 512 characters.
 * @author Mario Aichinger aichingm@gmail.com
 * @copyright (c) 2014, Mario Aichinger
 */
class MetaBlock extends \phtar\utils\abstracts\MetaBlock {

    private $name = 100;
    private $mode = 8;
    private $uid = 8;
    private $gid = 8;
    private $size = 12;
    private $mtime = 12;
    private $checksum = 8;
    private $linkflag = 1;
    private $linkname = 100;
    private $pad = 255;
    private $path;

    /**
     * 
     * @param string $path the path of the entity which the meta block should
     * represent. 
     */
    public function __construct($path) {
        $this->initArrays();
        $this->path = $path;
    }

    /**
     * Initialises the arrays with ascii null-bytes
     */
    private function initArrays() {
        $this->name = $this->createAsciiNullArray($this->name);
        $this->mode = $this->createAsciiNullArray($this->mode);
        $this->uid = $this->createAsciiNullArray($this->uid);
        $this->gid = $this->createAsciiNullArray($this->gid);
        $this->size = $this->createAsciiNullArray($this->size);
        $this->mtime = $this->createAsciiNullArray($this->mtime);
        $this->checksum = $this->createAsciiNullArray($this->checksum);
        $this->linkflag = $this->createAsciiNullArray($this->linkflag);
        $this->linkname = $this->createAsciiNullArray($this->linkname);
        $this->pad = $this->createAsciiNullArray($this->pad);
    }

    /**
     * Returns a new 512 characters long tar meta block.
     * @param array $inodes 
     * @return string
     */
    public function create(array $inodes) {

        $isHardLink = $this->isHardLink($inodes, $this->path);

        $this->writeStringToArrayStart($this->path, $this->name);
        $this->writeStringToArrayStart("0000" . substr(sprintf('%o', fileperms($this->path)), -3), $this->mode);
        $this->writeStringToArrayStart($this->prependCharToSize(sprintf('%o', fileowner($this->path)), 7, '0'), $this->uid);
        $this->writeStringToArrayStart($this->prependCharToSize(sprintf('%o', filegroup($this->path)), 7, '0'), $this->gid);
        if ($isHardLink) {
            $this->writeStringToArrayStart("00000000000", $this->size);
        } else {
            $this->writeStringToArrayStart($this->prependCharToSize(sprintf('%o', $this->filesize($this->path)), 11, '0'), $this->size);
        }
        $this->writeStringToArrayStart($this->prependCharToSize(sprintf('%o', filemtime($this->path)), 11, '0'), $this->mtime);
        $this->writeStringToArrayStart("        ", $this->checksum);
        $this->writeStringToArrayStart($this->getTypeflag($this->path), $this->linkflag);
        if ($isHardLink) {
            $this->writeStringToArrayStart($this->getHardLinkTo(), $this->linkname);
        }
        $sum = $this->calculateSum($this->__toString());
        $this->writeStringToArrayStart($this->prependCharToSize(sprintf('%o', $sum) . "\x00 ", 8, '0'), $this->checksum);

        return $this->__toString();
    }

    /**
     * Returns the type of the path, for more info checkout the "Summary of tar 
     * type codes" at the end of 
     * http://www.freebsd.org/cgi/man.cgi?query=tar&sektion=5 .
     * @param type $path the path of the file od directory
     * @return int
     */
    public function getTypeflag() {
        if ($this->getHardLinkTo() != "") {                                               // file is a hard link
            return 1;
        } else {                                                // file is a regular file;
            return 0;
        }
    }

    public function __toString() {
        return
                $this->implodeCharArray($this->name) .
                $this->implodeCharArray($this->mode) .
                $this->implodeCharArray($this->uid) .
                $this->implodeCharArray($this->gid) .
                $this->implodeCharArray($this->size) .
                $this->implodeCharArray($this->mtime) .
                $this->implodeCharArray($this->checksum) .
                $this->implodeCharArray($this->linkflag) .
                $this->implodeCharArray($this->linkname) .
                $this->implodeCharArray($this->pad);
    }

}
