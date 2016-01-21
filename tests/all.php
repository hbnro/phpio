<?php

require dirname(__DIR__).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';


chdir('tests');
mkdir('x/y/z', 0777, TRUE);
mkdir('a/b/c', 0777, TRUE);

IO\File::open('a/b/z', 'w+', function ($resource) {
  fwrite($resource, "x\ny");
});

IO\Dir::cpfiles('a/b', 'x', '*');

IO\File::each('a/b/z', function ($text, $line) {
  echo " #line $line => $text\n";
});

IO\Dir::each('.', '*', function ($file) {
  echo " #file => $file\n";
});

var_dump(IO\Helpers::expand('a/b/c/../d/e/./f/g/../h'));

$tmp = IO\Dir::findfile('a/b', '*', TRUE, 1);
$new = IO\File::read('a/b/z');
$old = IO\File::read('x/z');
$len = IO\Dir::size('.');

IO\Dir::unfile('a', '*', TRUE);
IO\Dir::unfile('x', '*', TRUE);

var_dump(IO\Dir::entries('.', '*', TRUE), $tmp, $new, $old, $len, IO\Helpers::fmtsize($len));
