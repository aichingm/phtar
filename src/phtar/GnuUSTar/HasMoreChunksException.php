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
 * This Exception get throwen if the path cant be represented with just one 
 * block of meta data for example if the metablock has a typeflag 
 * containing '''L''' 
 * @author Mario Aichinger aichingm@gmail.com
 * @copyright (c) 2014, Mario Aichinger
 */
class HasMoreChunksException extends \Exception{
    private $chunks = array();
    private $lastMeta = null;
    private $lastContent = null;
    /**
     * Returns the addidtional TarChunks in an array add them first to the 
     * TarArchive. Just if they added to the TarArchive it is sdave to add the
     * last block of meta data. 
     * @return array
     */
    public function getChunks() {
        return $this->chunks;
    }
    /**
     * Sets the array with the TarChunks.
     * @param array $chunks
     */
    public function setChunks(array $chunks) {
        $this->chunks = $chunks;
    }
    /**
     * Returns the last block of meta data.
     * @return string A string with the length 512
     */
    public function getLastMeta() {
        return $this->lastMeta;
    }
    /**
     * Returns the content of the last chuck if the content equals null check if
     * adding content is required 
     * @return string 
     */
    public function getLastContent() {
        return $this->lastContent;
    }
    /**
     * Sets the last block of meta data.
     * @param string $lastMeta
     */
    public function setLastMeta($lastMeta) {
        $this->lastMeta = $lastMeta;
    }
    /**
     * Sets the content of the last chunk. 
     * @param strgin $lastContent
     */
    public function setLastContent($lastContent) {
        $this->lastContent = $lastContent;
    }


}
