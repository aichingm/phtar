<?php

namespace phtar\utils;

/**
 * Description of ArchiveType
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
class ArchiveType {

    const TYPE_V7 = 0;
    const TYPE_V7_GTAR = "this is so stupid";
    const TYPE_GNU = 1;
    const TYPE_POSIX_USTAR = 2; # IEEE Std 1003.1-1988 (``POSIX.1'')
    const TYPE_PAX = 3;

    /**
     * Returns the type of the archive.
     * @param \phtar\utils\ReadFileFunctions $tarEntry
     * @return mixed
     */
    public static function entryType(\phtar\utils\ReadFileFunctions $tarEntry) {
        $tarEntry->seek(257);
        $magicVersion = $tarEntry->read(8);
        if ($magicVersion === "ustar  \0") {
            return self::TYPE_GNU;
        } elseif ($magicVersion === "ustar" . "\0" . "00") {
            return self::TYPE_POSIX_USTAR;
        } else if ($magicVersion === "\0\0\0\0\0\0\0\0") {
            $tarEntry->seek(257);
            if (self::onlyContains($tarEntry->read(255), "\0")) {
                return self::TYPE_V7;
            } else {
                return self::TYPE_V7_GTAR;
            }
        }
    }

    /**
     * Tests if the given string only conrains characters equal to $char
     * @param string $str
     * @param char $char
     * @return type
     */
    public static function onlyContains($str, $char) {
        if (strlen($char) != 1) {
            throw new \UnexpectedValueException("\$char needs to be a string only one character long");
        }
        $strInfo = count_chars($str, 3);
        return strlen($strInfo) == 1 && $strInfo == $char;
    }

}
