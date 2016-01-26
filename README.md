Talkin' bout I/O
================

A collection of helper classes to do many simple tasks through filesystem.

The installation is done using the [Composer](http://getcomposer.org/)/[Packagist](https://packagist.org/).

Quick reference
---------------

Retrieve the mime-type value from a given file

    IO\Helpers::mimetype($filename)

Return the formatted byte-length value

    IO\Helpers::fmtsize($bytes[, $unit = NULL[, $format = '%01.2f %s']])

Assemble and resolve paths from its arguments

    IO\Helpers::join($arg1[, $arg2[, $argN]])

Retrieve files in the given directory

    IO\Dir::entries($path[, $filter = '*'[, $recursive = FALSE]])

Copy files between directories

    IO\Dir::cpfiles($from, $to[, $filter = '*'[, $recursive = FALSE]])

Find files through given directory

    IO\Dir::findfile($path[, $filter = '*'[, $recursive = FALSE[, $index = 0]]])

Remove files from given directory

    IO\Dir::unfile($path[, $filter = '*'[, $recursive = FALSE]])

Retrieve the size in bytes from given directory

    IO\Dir::size($path[, $recursive = FALSE])

Execute the _lambda_ function for each file within the given directory using `opendir()` and pass the value from `readdir()` to the _lambda_ block. Then use `closedir()` after the whole iteration.

    IO\Dir::open($path, $lambda)

Execute the _lambda_ for each file within the given directory and pass the file path to the _lambda_ block recursively. This method use `entries()` to build its tree.

    IO\Dir::each($path, $filter, $lambda)

Get the filename extension

    IO\File::ext($name[, $dot = FALSE])

Get the whole filepath without extension

    IO\File::extn($name[, $base = FALSE])

Read a single file or URL

    IO\File::read($path)

Writes a single file

    IO\File::write($file, $content, $append)

Use `fopen()` and pass the _#resource_ to execute the _lambda_ block, then close the file using `fclose()` automatically.

    IO\File::open($file, $access, $lambda)

Execute the _lambda_ for each line from the given file.

    IO\File::each($path, $lambda)

## Contribute

The library performs just basic things, if you want add more features
or fix something, you're welcome.
