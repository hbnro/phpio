<?php

namespace IO;

class Resource
{

  private $res;
  private $path;
  private $buffer;

  public function __construct($path, $mode) {
    if (!($res = @fopen($path, $mode))) {
      throw new \Exception("The file '$path' could not be opened");
    }

    $this->res = $res;
    $this->path = $path;
    $this->buffer = (is_file($path) && filesize($path)) ? fread($this->res, filesize($path)) : '';
  }

  public function __destruct() {
    @fclose($this->res);
  }

  public function __toString() {
    $this->read();
  }

  public function prepend($content) {
    $this->buffer = $content . $this->buffer;

    return $this;
  }

  public function append($content) {
    $this->buffer .= $content;

    return $this;
  }

  public function write($content = NULL) {
    $this->buffer = $content ?: $this->buffer;

    fwrite($this->res, $this->buffer);

    return $this;
  }

  public function read() {
    return $this->buffer;
  }
}
