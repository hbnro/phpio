<?php

namespace IO;

class Dir
{

  public static function entries($from, $filter = '*', $recursive = FALSE)
  {
    if ( ! is_dir($from)) {
      throw new \Exception("The directory '$from' does not exists.");
    }

    $path = rtrim($from, DIRECTORY_SEPARATOR);

    $paths = glob($path.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR);
    $files = glob($path.DIRECTORY_SEPARATOR.$filter, GLOB_MARK | GLOB_BRACE);

    if ($recursive) {
      foreach ($paths as $one) {
        $test  = static::entries($one, $filter, $recursive);
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

    $path = preg_quote($from, '/');
    $test = static::entries($from, $filter, $recursive);

    foreach ($test as $file) {
      $new = preg_replace("/^$path/", $to, $file);

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

    $output = static::entries($path, $filter, $recursive);
    $output = array_values(array_filter($output, 'is_file'));

    if ($index > 0) {
      return isset($output[$index - 1]) ? $output[$index - 1] : FALSE;
    }

    return $output;
  }

  public static function unfile($path, $filter = '*', $recursive = FALSE)
  {
    if ( ! is_dir($path)) {
      throw new \Exception("The directory '$path' does not exists.");
    }

    $test = array_reverse(static::entries($path, $filter, $recursive));

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
    $test = array_filter(static::entries($path, '*', $recursive), 'is_file');

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
    $set = static::entries($path, $filter, TRUE);

    foreach ($set as $nth => $file) {
      $lambda($file, $nth);
    }
  }

}
