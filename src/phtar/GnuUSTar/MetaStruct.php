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

namespace phtar\GnuUSTar;

/**
 * This is a helper class for the creation of gnu ustar meta blocks.
 * @author Mario Aichinger aichingm@gmail.com
 * @copyright (c) 2014, Mario Aichinger
 */
class MetaStruct {

    public $name = 100;
    public $mode = 8;
    public $uid = 8;
    public $gid = 8;
    public $size = 12;
    public $mtime = 12;
    public $checksum = 8;
    public $typeflag = 1;
    public $linkname = 100;
    public $magic = 6;
    public $version = 2;
    public $uname = 32;
    public $gname = 32;
    public $devmajor = 8;
    public $devminor = 8;
    public $atime = 12;
    public $ctime = 12;
    public $offset = 12;
    public $longnames = 4;
    public $unused = 1;
    public $sparse = 96;
    public $isextended = 1;
    public $realsize = 12;
    public $pad = 17;

    public function __construct() {
        $this->initArrays();
    }

    /**
     * Returns a \0 characters filled array with a length of $size.
     * @param int $size
     * @return array 
     */
    protected function createAsciiNullArray($size) {
        $array = array();
        for ($i = 0; $i < $size; $i++) {
            $array[$i] = "\0";
        }
        return $array;
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
        $this->typeflag = $this->createAsciiNullArray($this->typeflag);
        $this->linkname = $this->createAsciiNullArray($this->linkname);
        $this->magic = $this->createAsciiNullArray($this->magic);
        $this->version = $this->createAsciiNullArray($this->version);
        $this->uname = $this->createAsciiNullArray($this->uname);
        $this->gname = $this->createAsciiNullArray($this->gname);
        $this->devmajor = $this->createAsciiNullArray($this->devmajor);
        $this->devminor = $this->createAsciiNullArray($this->devminor);
        $this->atime = $this->createAsciiNullArray($this->atime);
        $this->ctime = $this->createAsciiNullArray($this->ctime);
        $this->offset = $this->createAsciiNullArray($this->offset);
        $this->longnames = $this->createAsciiNullArray($this->longnames);
        $this->unused = $this->createAsciiNullArray($this->unused);
        $this->sparse = $this->createAsciiNullArray($this->sparse);
        $this->isextended = $this->createAsciiNullArray($this->isextended);
        $this->realsize = $this->createAsciiNullArray($this->realsize);
        $this->pad = $this->createAsciiNullArray($this->pad);
    }

    /**
     * Returns the string representation of the block.
     * @return string
     */
    public function __toString() {
        return
                implode('', $this->name) .
                implode('', $this->mode) .
                implode('', $this->uid) .
                implode('', $this->gid) .
                implode('', $this->size) .
                implode('', $this->mtime) .
                implode('', $this->checksum) .
                implode('', $this->typeflag) .
                implode('', $this->linkname) .
                implode('', $this->magic) .
                implode('', $this->version) .
                implode('', $this->uname) .
                implode('', $this->gname) .
                implode('', $this->devmajor) .
                implode('', $this->devminor) .
                implode('', $this->atime) .
                implode('', $this->ctime) .
                implode('', $this->offset) .
                implode('', $this->longnames) .
                implode('', $this->unused) .
                implode('', $this->sparse) .
                implode('', $this->isextended) .
                implode('', $this->realsize) .
                implode('', $this->pad);
    }

}
