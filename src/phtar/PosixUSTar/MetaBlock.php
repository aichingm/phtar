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

namespace phtar\PosixUSTar;

/**
 * This Exeption gets thrown if the path is to long for one meta block and has 
 * no '''/''' in it where the path could be splited 
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
    private $typeflag = 1;
    private $linkname = 100;
    private $magic = 6;
    private $version = 2;
    private $uname = 32;
    private $gname = 32;
    private $devmajor = 8;
    private $devminor = 8;
    private $prefix = 155;
    private $pad = 12;
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
        $this->typeflag = $this->createAsciiNullArray($this->typeflag);
        $this->linkname = $this->createAsciiNullArray($this->linkname);
        $this->magic = $this->createAsciiNullArray($this->magic);
        $this->version = $this->createAsciiNullArray($this->version);
        $this->uname = $this->createAsciiNullArray($this->uname);
        $this->gname = $this->createAsciiNullArray($this->gname);
        $this->devmajor = $this->createAsciiNullArray($this->devmajor);
        $this->devminor = $this->createAsciiNullArray($this->devminor);
        $this->prefix = $this->createAsciiNullArray($this->prefix);
        $this->pad = $this->createAsciiNullArray($this->pad);
    }

    /**
     * Returns a new 512 characters long tar meta block.
     * @param array $inodes 
     * @return string
     */
    public function create(array $inodes) {

        $isHardLink = $this->isHardLink($inodes, $this->path);

        $parts = $this->splitPath($this->path);
        $this->writeStringToArrayStart($parts[0], $this->prefix);
        $this->writeStringToArrayStart($parts[1], $this->name);

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
        $this->writeStringToArrayStart($this->getTypeflag($this->path), $this->typeflag);
        if ($isHardLink) {
            $this->writeStringToArrayStart($this->getHardLinkTo(), $this->linkname);
        }
        $this->writeStringToArrayStart("ustar", $this->magic);
        $this->writeStringToArrayStart("00", $this->version);
        $user = posix_getpwuid(fileowner($this->path));
        $this->writeStringToArrayStart($user['name'], $this->uname);
        $group = posix_getgrgid(filegroup($this->path));
        $this->writeStringToArrayStart($group['name'], $this->gname);



        $sum = $this->calculateSum($this->__toString());
        $this->writeStringToArrayStart($this->prependCharToSize(sprintf('%o', $sum) . "\x00 ", 8, '0'), $this->checksum);
        return $this->__toString();
    }

    public function splitPath($path) {
        if (strlen($path) > 99) {
            if (strlen($path) < 254 && strpos($path, '/') !== false) {
                $index = strpos($path, '/', strlen($path) - 99);
                return array(substr($path, 0, $index), substr($path, $index + 1));
            } else {
                return array("", substr($path, -99));
            }
        } else {
            return array("", $path);
        }
    }

    /**
     * Returns the type of the path, for more info checkout the "Summary of tar 
     * type codes" at the end of 
     * http://www.freebsd.org/cgi/man.cgi?query=tar&sektion=5 .
     * @param type $path the path of the file od directory
     * @return int
     */
    public function getTypeflag($path) {
        if ($this->getHardLinkTo() != "") { // file is a hard link
            return 1;
        } elseif (is_link($path)) {         // file is a soft link
            return 2;
        } elseif (false) {                  // file is a cahracter device node UNIMPLEMENTED
            return 3;
        } elseif (false) {                  // file is a block device node UNIMPLEMENTED
            return 4;
        } elseif (is_dir($path)) {          // path points to a directory
            return 5;
        } elseif (false) {                  // file is a FIFO node UNIMPLEMENTED
            return 6;
        } else {                            // file is a regular file;
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
                $this->implodeCharArray($this->typeflag) .
                $this->implodeCharArray($this->linkname) .
                $this->implodeCharArray($this->magic) .
                $this->implodeCharArray($this->version) .
                $this->implodeCharArray($this->uname) .
                $this->implodeCharArray($this->gname) .
                $this->implodeCharArray($this->devmajor) .
                $this->implodeCharArray($this->devminor) .
                $this->implodeCharArray($this->prefix) .
                $this->implodeCharArray($this->pad);
    }

}
