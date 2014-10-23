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
 * A class which is able to generate gnu urtar metablocks in the length of 512 
 * characters. Unless this class is designed to generate gnu urtar metablocks it
 * is not able to work with the S flag in the "tar -Scf" command simply because 
 * it is not implemented. Maybe I will implement it some time in the future.
 * @author Mario Aichinger aichingm@gmail.com
 * @copyright (c) 2014, Mario Aichinger
 */
class MetaBlock extends \phtar\utils\abstracts\MetaBlock {

    private $path;
    private $stringContentBlockFactory;

    public function __construct($path) {
        $this->path = $path;
        $this->stringContentBlockFactory = new \phtar\utils\StringContentFactory();
    }

    /**
     * Returns a new 512 characters long tar meta block. If the metablock is for
     * example a type '''L''' block the method throws a  HasMoreChunksException 
     * which means that the file can not be represented with just one block of 
     * meta data. This mostly happens if the file name is to long (longer then 
     * 100 character). Else if all the data fits in one block the generated 
     * block gets returnd as a string with a length of 512.
     * @param array $inodes
     * @return string
     * @throws \phtar\GnuUSTar\HasMoreChunksException
     */
    public function create(array $inodes) {
        if (strlen($this->path) > 100) {
            $exception = new HasMoreChunksException();
            $longNameChunks = $this->createLongName();
            $exception->setChunks(array($longNameChunks[0] => $longNameChunks[1]));
        }
        $metaBlock = $this->createRegular($inodes);


        if (isset($exception)) {
            $exception->setLastMeta($metaBlock);
            throw $exception;
        }
        return $metaBlock;
    }

    /**
     * Returns an array with the meta an the content block for long filenames (longer the 100 characters).
     * @return array The index 0 conains the metablock as strgin and the index 1 contains the content
     */
    private function createLongName() {
        $struct = new MetaStruct();
        $this->writeStringToArrayStart("././@LongLink", $struct->name);
        $this->writeStringToArrayStart("0000" . substr(sprintf('%o', fileperms($this->path)), -3), $struct->mode);
        $this->writeStringToArrayStart($this->prependCharToSize(0, 7, '0'), $struct->uid);
        $this->writeStringToArrayStart($this->prependCharToSize(0, 7, '0'), $struct->gid);
        $this->writeStringToArrayStart($this->prependCharToSize(sprintf('%o', strlen($this->path)), 11, '0'), $struct->size);
        $this->writeStringToArrayStart($this->prependCharToSize(sprintf('%o', filemtime($this->path)), 11, '0'), $struct->mtime);
        $this->writeStringToArrayStart("        ", $struct->checksum);
        $this->writeStringToArrayStart("L", $struct->typeflag);
        $this->writeStringToArrayStart("ustar ", $struct->magic);
        $this->writeStringToArrayStart(" \x00", $struct->version);
        $this->writeStringToArrayStart("root", $struct->uname);
        $this->writeStringToArrayStart("root", $struct->gname);
        $sum = $this->calculateSum($struct);
        $this->writeStringToArrayStart($this->prependCharToSize(sprintf('%o', $sum) . "\x00 ", 8, '0'), $struct->checksum);
        return array(0 => $struct . "", 1 => $this->stringContentBlockFactory->create($this->path));
    }

    /**
     * Returns a gnu ustar metablock.
     * @param array $inodes An array with all inodes => paths which are already 
     * added to the array this is neaded to handle hardlinks correctly
     * @return string The metablock with a length of 512 
     */
    public function createRegular($inodes) {
        $struct = new MetaStruct();
        $isHardLink = $this->isHardLink($inodes, $this->path);
        $this->writeStringToArrayStart($this->path, $struct->name);
        $this->writeStringToArrayStart("0000" . substr(sprintf('%o', fileperms($this->path)), -3), $struct->mode);
        $this->writeStringToArrayStart($this->prependCharToSize(sprintf('%o', fileowner($this->path)), 7, '0'), $struct->uid);
        $this->writeStringToArrayStart($this->prependCharToSize(sprintf('%o', filegroup($this->path)), 7, '0'), $struct->gid);
        if ($isHardLink) {
            $this->writeStringToArrayStart("00000000000", $struct->size);
        } else {
            $this->writeStringToArrayStart($this->prependCharToSize(sprintf('%o', $this->filesize($this->path)), 11, '0'), $struct->size);
        }
        $this->writeStringToArrayStart($this->prependCharToSize(sprintf('%o', filemtime($this->path)), 11, '0'), $struct->mtime);
        $this->writeStringToArrayStart("        ", $struct->checksum);
        $this->writeStringToArrayStart($this->getTypeflag($this->path), $struct->typeflag);
        if ($isHardLink) {
            $this->writeStringToArrayStart($this->getHardLinkTo(), $struct->linkname);
        }
        $this->writeStringToArrayStart("ustar ", $struct->magic);
        $this->writeStringToArrayStart(" \x00", $struct->version);
        $user = posix_getpwuid(fileowner($this->path));
        $this->writeStringToArrayStart($user['name'], $struct->uname);
        $group = posix_getgrgid(filegroup($this->path));
        $this->writeStringToArrayStart($group['name'], $struct->gname);
        $this->writeStringToArrayStart($this->prependCharToSize(sprintf('%o', fileatime($this->path)) . "\x00 ", 12, '0'), $struct->atime);
        $this->writeStringToArrayStart($this->prependCharToSize(sprintf('%o', filectime($this->path)) . "\x00 ", 12, '0'), $struct->ctime);


        $sum = $this->calculateSum($struct . "");
        $this->writeStringToArrayStart($this->prependCharToSize(sprintf('%o', $sum) . "\x00 ", 8, '0'), $struct->checksum);
        return $struct->__toString();
    }

    /**
     * Returns the type of the path, for more info checkout the "Summary of tar 
     * type codes" at the end of 
     * http://www.freebsd.org/cgi/man.cgi?query=tar&sektion=5 . Maybe a bug but 
     * it works for now: if the path points to a directory it 
     * returns '''5''' and not '''D'''
     * @param type $path the path of the file od directory
     * @return int
     */
    public function getTypeflag($path) {
        if ($this->getHardLinkTo() != "") {                                               // file is a hard link
            return 1;
        } elseif (is_link($path)) {                              // file is a soft link
            return 2;
        } elseif (is_dir($path)) {                              // path points to a directory
            return 5;
        } else {                                                // file is a regular file;
            return 0;
        }
    }

}
