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
namespace phtar\utils;
/**
 * This class helps you to get inforamtion out of an tar meta block.
 * @author Mario Aichinger aichingm@gmail.com
 * @copyright (c) 2014, Mario Aichinger
 */
class MetaBlockAnalyser {
    
    public static $TYPE_GNU_OLD = 0;
    public static $TYPE_POSIX_US_TAR = 1;
    public static $TYPE_GNU_US_TAR = 2;
    
    /**
     * Checks if a meta block matches its chaecksum
     * @param string $metaBlock
     * @return boolean
     */
    public static function validateChecksum($metaBlock) {
        if (is_string($metaBlock) && strlen($metaBlock) == 512) {
            $checksum = "";
            for ($i = 148; $i < 156; $i++) {
                if(in_array($metaBlock{$i}, array('0','1','2','3','4','5','6','7'))){
                     $checksum .= $metaBlock{$i};
                }
                $metaBlock{$i} = " ";
            }
            $sum = MetaBlockAnalyser::calculateChecksumOctal($metaBlock);
            return intval($checksum) == intval($sum);
        } else {
            return false;
        }
    }
    /**
     * Returns the decimal integer interpretation of the sum of all 
     * characters in the metablock. Note taht if you want to get a valid 
     * checksum you have to set all checksum characters to a space ''' '''
     * @param string $metaBlock
     * @return int
     */
    public static function calculateChecksumOctal($metaBlock) {
        for ($i = 148; $i < 156; $i++) {
                $metaBlock{$i} = " ";
            }
        $byte_array = unpack('C*', $metaBlock);
        $sum = 0;
        foreach ($byte_array as $char) {
            $sum += $char;
        }
        return decoct($sum);
    }
    /**
     * Returns the char in the typeflag register.
     * @param string $metaBlock
     * @return string
     */
    public static function getTypeflag ($metaBlock){
        if (is_string($metaBlock) && strlen($metaBlock) == 512) {
            return $metaBlock{156};
        }else{
            return "-1";
        }
    }
    /**
     * Returns the size of the next content as a decimal integer. Note taht this
     * is the actual size of the file and not the size of the next
     * content block.
     * @param string $metablock
     * @return int
     */
    public static function getSize($metablock) {
        if (is_string($metablock) && strlen($metablock) == 512) {
            $size = "";
            for ($i = 124; $i < 136; $i++) {
                $size .= $metablock{$i};
            }
            return octdec(intval($size));
        } else {
            return 0;
        }
    }
    /**
     * Returns the gnu old name of the file
     * @param string $metablock
     * @return string
     */
    public static function getName($metablock){
        $name = "";
        for($i = 0; $i < 100;$i++){
            if($metablock{$i} != "\0"){
                $name .= $metablock{$i};
            }else{
                break;
            }
        }
        return $name;
    }
    /**
     * Returns the gnu old name of the file
     * @param string $metablock
     * @return string
     */
    public static function getPrefix($metablock){
        $name = "";
        for($i = 345; $i < 155;$i++){
            if($metablock{$i} != "\0"){
                $name .= $metablock{$i};
            }else{
                break;
            }
        }
        return $name;
    }
    /**
     * Returns the type of the meta data gnuold gnuUsTar or posixUsTar
     * @param string $metablock
     * @return int
     */
    public static function getTarType($metablock){
        $magic = substr($metablock, 257, 8);
        if($magic === "ustar  \0"){
            return MetaBlockAnalyser::$TYPE_GNU_US_TAR;
        }elseif($magic === "ustar\0"."00"){
            return MetaBlockAnalyser::$TYPE_POSIX_US_TAR;
        }else{
            return MetaBlockAnalyser::$TYPE_GNU_OLD;
        }
    }

}
