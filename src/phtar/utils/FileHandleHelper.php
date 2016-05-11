<?php

namespace phtar\utils;

/**
 * Description of FileHandleHelper
 * 
 * @author Mario Aichinger <aichingm@gmail.com>
 */
class FileHandleHelper {

    /**
     * Clones a resource/file-descriptor and optional seeks to the same position
     * @param resource $fd file descriptor
     * @param int $initSeek if set to a value from -1 up to the size of the resource this function will seek the cloned resource to the same position as the given file descriptor
     * @return resource
     * @throws \UnexpectedValueException if the $fd is not a resource or if $fd is not a file descriptor (wrapper_type!== plainfile or stream_type !== STDIO) 
     */
    public static function CLONE_HANDLE($fd, $initSeek = -1) {
        if (!is_resource($fd)) {
            throw new \UnexpectedValueException("not a resource");
        }
        $meta = stream_get_meta_data($fd);
        if
        ($meta['wrapper_type'] !== 'plainfile' ||
                $meta['stream_type'] !== 'STDIO') {
            throw new \UnexpectedValueException("not a file descriptor");
        }
        if ($initSeek == -1) {
            $initSeek = ftell($fd);
        }
        $clone_fd = fopen($meta['uri'], $meta['mode']);
        fseek($clone_fd, $initSeek);
        return $clone_fd;
    }

    /**
     * Copys the content of $from to the $to (\phtar\utils\WriteFileFunctions) object. Returns the number of bytes copyed this way. 
     * @param \phtar\utils\WriteFileFunctions $from 
     * @param \phtar\utils\WriteFileFunctions $to
     * @param int $bufferSize copys cunks of $bufferSize 
     * @return int 
     */
    public static function COPY_H2H(ReadFileFunctions $from, WriteFileFunctions $to, $bufferSize = 512) {
        $from->seek(0);
        $bytesWriten = 0;
        while (!$from->eof()) {
            $bytesWriten += $to->write($from->read($bufferSize));
        }
        return $bytesWriten;
    }

}
