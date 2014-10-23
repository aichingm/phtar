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

class TarReaderFactory {

    public static function getReader(utils\interfaces\TarArchive $tarArchive) {
        $chunks = $tarArchive->getChunks(); 
        if (count($chunks) > 0) {
            if (utils\MetaBlockAnalyser::getTarType($chunks[0]->getMeta()) == utils\MetaBlockAnalyser::$TYPE_GNU_OLD) {
                return new GnuOldTar\TarReader($tarArchive);
            } elseif (utils\MetaBlockAnalyser::getTarType($chunks[0]->getMeta()) == utils\MetaBlockAnalyser::$TYPE_GNU_US_TAR) {
                return new GnuUSTar\TarReader($tarArchive);
            } elseif (utils\MetaBlockAnalyser::getTarType($chunks[0]->getMeta()) == utils\MetaBlockAnalyser::$TYPE_POSIX_US_TAR) {
                return new PosixUSTar\TarReader($tarArchive);
            }
        }
        return null;
    }

}
