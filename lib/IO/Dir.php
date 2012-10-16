<?php

namespace IO;

class Dir
{

  const RECURSIVE = 1;
  const SORTING = 2;



  public static function entries($from, $filter = '*', $options = 0)
  {
    if ( ! is_dir($from)) {
      throw new \Exception("The directory '$from' does not exists.");
    }

    $path = rtrim($from, DIRECTORY_SEPARATOR);
    $sort = ((int) $options & static::SORTING) == 0 ? FALSE : TRUE;
    $recursive = ((int) $options & static::RECURSIVE) == 0 ? FALSE : TRUE;

    $paths = glob($path.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR | ( ! $sort ? GLOB_NOSORT : 0));
    $files = glob($path.DIRECTORY_SEPARATOR.$filter, GLOB_MARK | GLOB_BRACE | ( ! $sort ? GLOB_NOSORT : 0));

    if ($recursive) {
      foreach ($paths as $one) {
        $test  = static::entries($one, $filter, $options);
        $files = array_merge($files, $test);
      }
    }

    return $files;
  }

  public static function cpfiles($from, $to, $filter = '*', $recursive = FALSE)
  {
    if ( ! is_dir($from)) {
      throw new \Exception("The directory '$from' does not exists.");
    }

    is_dir($to) OR mkdir($to, 0777, TRUE);

    $options = $recursive ? static::RECURSIVE : 0;
    $test    = static::entries($from, $filter, $options | static::SORTING);

    foreach ($test as $file) {
      $new = str_replace(realpath($from), $to, $file);

      if ( ! file_exists($new)) {
        is_dir($file) && mkdir($new, 0777);
        is_file($file) && copy($file, $new);
      }
    }
  }

  public static function findfile($path, $filter = '*', $recursive = FALSE, $index = 0)
  {
    if ( ! is_dir($path)) {
      throw new \Exception("The directory '$path' does not exists.");
    }

    $recursive = $recursive ? static::RECURSIVE : 0;
    $output    = array_filter(static::entries($path, $filter, $recursive | static::SORTING), 'is_file');

    if ($index > 0) {
      return isset($output[$index - 1]) ? $output[$index - 1] : FALSE;
    }

    return $output;
  }

  public static function unfile($path, $filter = '*', $options = FALSE)
  {
    if ( ! is_dir($path)) {
      throw new \Exception("The directory '$path' does not exists.");
    }

    $test = array_reverse(static::entries($path, $filter, $options | static::SORTING));

    foreach ($test as $one) {
      is_file($one) && @unlink($one);
      is_dir($one) && @rmdir($one);
    }

    @rmdir($path);

    return TRUE;
  }

  public static function size($path, $recursive = FALSE)
  {
    if ( ! is_dir($path)) {
      throw new \Exception("The directory '$path' does not exists.");
    }


    $out = 0;
    $test = array_filter(static::entries($path, '*', $recursive ? static::RECURSIVE : 0), 'is_file');

    foreach ($test as $old) {
      $out += filesize($old);
    }

    return $out;
  }

  public static function open($path, \Closure $lambda)
  {
    if ( ! ($res = @opendir($path))) {
      throw new \Exception("The directory '$path' could not be opened.");
    }

    while (($tmp = @readdir($res)) !== FALSE) {
      if (($tmp <> '.') && ($tmp <> '..')) {
        $lambda($tmp);
      }
    }
    @closedir($res);
  }

  public static function each($path, $filter, \Closure $lambda)
  {
    // TODO: should be recursive?
    $set = static::entries($path, $filter, static::SORTING);

    foreach ($set as $nth => $file) {
      $lambda($file, $nth);
    }
  }

}
