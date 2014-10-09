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

namespace phtar;

/**
 * TarChunk objects holds a block of meta data with a length of 512 and a 
 * content block 
 * @author Mario Aichinger aichingm@gmail.com
 * @copyright (c) 2014, Mario Aichinger
 */
class TarChunk implements utils\interfaces\TarChunk {

    /**
     * Holds the meta data
     * @var string 
     */
    private $meta;

    /**
     * Holds the content data as raw content data which means the the content 
     * data has a \0 characters appended to it to fit in a whole block. You can 
     * get the actual size of the content from the tra data block.
     * @var string 
     */
    private $rawContent = "";

    public function &getMeta() {
        return $this->meta;
    }

    /**
     * Returns the content data as raw content data which means the the content 
     * data has a \0 characters appended to it to fit in a whole block. You can 
     * get the actual size of the content from the tra data block.
     * @return string
     */
    public function &getRawContent() {
        return $this->rawContent;
    }

    /**
     * Sets the meta data.
     * @param string $meta
     * @throws utils\InvalidBlockLengthException
     */
    public function setMeta($meta) {
        if (strlen($meta) == 512) {
            $this->meta = $meta;
        } else {
            throw new utils\InvalidBlockLengthException("Unexpected block length " . strlen($meta));
        }
    }

    /**
     * Sets the rawcontent data. Raw means the data as it would be in a tar 
     * archive.
     * @param string $content
     * @throws utils\InvalidBlockLengthException
     */
    public function setRawContent($content) {
        if (strlen($content) % 512 == 0) {
            $this->rawContent = $content;
        } else {
            throw new utils\InvalidBlockLengthException("Unexpected block length " . strlen($content));
        }
    }

}
