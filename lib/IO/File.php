<?php

namespace IO;

class File
{

  public static function ext($from, $dot = FALSE)
  {
    $out = pathinfo($from, PATHINFO_EXTENSION);
    $out = $dot ? ".$out" : $out;

    return $out;
  }

  public static function extn($from, $base = FALSE)
  {
    $ext  = static::ext($from, TRUE);

    $from = substr($from, 0, -strlen($ext));
    $from = $base ? basename($from) : $from;

    return $from;
  }

  public static function read($path)
  {
    $output = FALSE;

    if (strpos($path, '://') !== FALSE) {
      $test = @parse_url($path);

      $port  = ! empty($test['port']) ? $test['port'] : 80;
      $guri  = ! empty($test['path']) ? $test['path'] : '/';
      $guri .= ! empty($test['query']) ? "?$test[query]" : '';

      $agent = 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)';


      if ($test['scheme'] === 'https') {
        $port = 433;
      }

      if (ini_get('allow_url_fopen')) {
        $output = file_get_contents($path);
      } elseif (function_exists('curl_init')) {
        $resource = curl_init();

        curl_setopt($resource, CURLOPT_URL, "$test[scheme]://$test[host]$guri");

        curl_setopt($resource, CURLOPT_FAILONERROR, 1);
        curl_setopt($resource, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($resource, CURLOPT_PORT, $port);
        curl_setopt($resource, CURLOPT_TIMEOUT, 90);
        curl_setopt($resource, CURLOPT_USERAGENT, $agent);

        $output = curl_exec($resource);
      } elseif (function_exists('fsockopen')) {
        $resource = @fsockopen($test['host'], $port, $errno, $errstr, 90);

        if (is_resource($resource)) {
          fputs($resource, "GET $guri HTTP/1.0\r\n");
          fputs($resource, "Host: $test[host]\r\n");
          fputs($resource, "User-Agent: $agent\r\n");
          fputs($resource, "Accept: */*\r\n");
          fputs($resource, "Accept-Language: en-us,en;q=0.5\r\n");
          fputs($resource, "Accept-Charset: iso-8859-1,utf-8;q=0.7,*;q=0.7\r\n");
          fputs($resource, "Keep-Alive: 300\r\n");
          fputs($resource, "Connection: Keep-Alive\r\n");

          $end = FALSE;

          while ( ! feof($resource)) {// http://www.php.net/manual/en/function.fsockopen.php#87144
            $tmp = @fgets($resource, 128);

            if ($tmp === "\r\n") {
              $end = TRUE;
            }

            if ($end) {
              $output .= $tmp;
            }
          }
          fclose($resource);
        }
      }
    } elseif (is_file($path)) {
      $output = file_get_contents($path);
    }


    if (substr($output, 0, 3) === "\xEF\xBB\xBF") {// TODO: avoid possible BOM issue?
      $output = substr($output, 3);
    }

    return $output;
  }

  public static function write($to, $content = '', $append = FALSE)
  {
    return @file_put_contents($to, $content, $append ? FILE_APPEND : 0) !== FALSE;
  }

}
