phtar
=====

An implementation of the tar file archive format (v7, posix(ustar), gnutar) written in php.

### Read An Archive ###

To read an archive from a file use this code:

    //load archive from file
    $archive = phtar\Archive::OPEN_BY_FILE_NAME("path/to/the/archive.tar");
    
    //iterate over the archive
    foreach($archive as $file){
    
        echo $file->getName() . "\n";
        echo $file->getContent();   
    }
    
    //find a file in the archive
    $archiveEntry = $archive->find("path/to/my/file.txt");

__Note:__ `$archive` is of the type `phtar\v7\Archive`, `phtar\posix\Archive` or `phtar\gnu\Archive` depending on the type of archive.

__Note:__ `$file` is of the type `phtar\v7\ArchiveEntry`, `phtar\posix\ArchiveEntry` or `phtar\gnu\ArchiveEntry` depending on the type of archive. Check this classes for more methods.

### Write An Archive ###


    //open file handle
    $fh = new FileHandle(fopen("myArchive.tar", "r+"));
    //create archive creator
    $ac = new \phtar\v7\ArchiveCreator($fh);
    //create DirectoryArchiver pass ArchiveCreator and directory
    $directoryArchiver = new DirectoryArchiver($ac, "archive/this/dir");
    //add the files and directories to the archive
    $directoryArchiver->archive();
    //add an entry to the archive
    $ac->add(new \phtar\v7\BaseEntry("myFile.txt", "[the files contents]"));
    //write the archive to the file
    $ac->write();



__Note:__ When opening the file handle it is important to use `r+` as the mode.

## How To ##
For now there is no better documentation than the tests in `tests/`.

## Tests ##

Run the test:

__Note:__ You will need the Pest to run the test. Get your copy at https://github.com/aichingm/Pest

    php path/to/Pest.php tests/

This will run all tests. To check which failed use:

    php path/to/Pest.php tests/ --pest_only_failed

## License ##

GNU GENERAL PUBLIC LICENSE Version 3. See the `LICENSE` file in the repos root.

---
Copyright (C) 2016  Mario Aichinger

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

