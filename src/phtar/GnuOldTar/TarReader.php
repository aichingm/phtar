<?php

/*
 * This file is part of: maLib - Mario Aichinger Library
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

use \phtar\utils\MetaBlockAnalyser;
use \phtar\utils\interfaces\TarChunk;
use \phtar\utils\interfaces\TarArchive;

/**
 * This class should simplify the work with TarArchives by just returning 
 * files and folder. Note that the implementation of this class is very very 
 * primitive so do not expect much from it if you need a more complete 
 * implmentation you can use the cod of this class to create your own TarReader.
 * @author Mario Aichinger aichingm@gmail.com
 * @copyright (c) 2014, Mario Aichinger
 */
class TarReader implements \phtar\utils\interfaces\TarReader {

    /**
     * Holds an array of TarChunks
     * @var array 
     */
    private $chunks;

    /**
     * Holds the current position in the array used for walking through the array.
     * @var int 
     */
    private $pointer = 0;

    /**
     * Creates a new TarReader.
     * @param TarArchive $archive
     */
    public function __construct(TarArchive $archive) {
        //save all TarChunks to $this->chuks
        $this->chunks = $archive->getChunks();
    }

    /**
     * Returns an array representing an entry in the archive. 
     * array("name" => ...,"type" => ..., "content" => ...) the type is -1 if 
     * an error occurs, 0 in case of an file and 5 in case the entry is 
     * a directory.
     * @return array
     */
    public function getNextFileOrDirectory() {
        //store the meta data of the current chunk in $meta
        $meta = $this->chunks[$this->pointer]->getMeta();
        //investigate the typle of the chunk this may varies in other types of 
        //tar standards or implementations
        $type = MetaBlockAnalyser::getTypeflag($meta);
        //create an empty array which will be returned later
        $array = array();
        switch ($type) {
            //if the $type equals '''0''' or '''\0''' the chunk is a file
            case '0':
            case '\0':
                //set the type of the chunk into the array
                $array['typeflag'] = 0;
                //set the name of the entry
                $array['name'] = MetaBlockAnalyser::getName($meta);
                //add the content into the array
                $array['content'] = $this->getContentWithSize($this->chunks[$this->pointer]);
                break;
            //if the $type equals '''5''' the entry is a directory
            case '5':
                //set the type in the array
                $array['typeflag'] = 5;
                //set the name into the array
                $array['name'] = MetaBlockAnalyser::getName($meta);
                break;
            default:
                //create a default array with the type '''-1''' to indecate 
                //that an error occured
                $array = array("typeflag" => "-1", "name" => "undefind", "content" => "");
        }
        //increase the current position
        $this->pointer++;
        //return the array
        return $array;
    }

    /**
     * Returns the content of the TarChunk without the tailing \0
     * @param \phtar\utils\interfaces\TarChunk $tarChunk
     * @return string
     */
    private function getContentWithSize(TarChunk $tarChunk) {
        //store the size of the content to the $sizeDec var
        $sizeDec = MetaBlockAnalyser::getSize($tarChunk->getMeta());
        //return a substring of the content form 0 to the length of $sizeDec
        return substr($tarChunk->getRawContent(), 0, $sizeDec);
    }

    /**
     * Returns true if the archive has a next entry or false if no not.
     * @return boolean
     */
    public function hasNext() {
        //return true if the archive has another chunk
        if (count($this->chunks) > $this->pointer) {
            return true;
        }
        return false;
    }

    public function reset() {
        $this->pointer = 0;
    }

}
