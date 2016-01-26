<?php

describe('IO', function() {
  describe('Dir', function() {
  });

  describe('File', function() {
    it('ext() should return a file extension', function() {
      expect(\IO\File::ext('a.b.c'))->toEqual('c');
      expect(\IO\File::ext('a.b.c', TRUE))->toEqual('.c');
    });

    it('extn() should return a file without extension', function() {
      expect(\IO\File::extn('x/a.b.c'))->toEqual('x/a.b');
      expect(\IO\File::extn('x/a.b.c', TRUE))->toEqual('a.b');
    });

    it('read() and write() works without issues', function() {
      $tmpfile = \IO\Helpers::tmp(uniqid('readwrite'));

      \IO\File::write($tmpfile, 'OK');

      expect(\IO\File::read($tmpfile))->toEqual('OK');
    });

    it('open() should return and wrap a \\IO\\Resource', function() {
      $tmpfile = \IO\Helpers::tmp(uniqid('open'));

      $file = \IO\File::open($tmpfile, 'x+', function($r) {
        expect($r->read())->toEqual('');
        expect($r->write('x')->read())->toEqual('x');
      });

      expect($file->append('a')->read())->toEqual('xa');
      expect($file->prepend('b')->read())->toEqual('bxa');
    });

    it('each() should iterate all lines from given file', function() {
      $tmpfile = \IO\Helpers::tmp(uniqid('each'));

      \IO\File::open($tmpfile, 'x+')->write("a\nb\nc");

      $lines = array();

      \IO\File::each($tmpfile, function($line) use (&$lines) {
        $lines []= $line;
      });

      expect($lines)->toEqual(array('a', 'b', 'c'));
    });
  });

  describe('Helpers', function() {
    it('mimetype() should report properly', function() {
      expect(\IO\Helpers::mimetype('txt'))->toEqual('text/plain');
    });

    it('fmtsize() should report properly', function() {
      expect(\IO\Helpers::fmtsize(1234567890))->toEqual('1.23 G');
      expect(\IO\Helpers::fmtsize(1234567890, 'B'))->toEqual('1234567890.00 B');
    });

    it('join() should resolve properly', function() {
      expect(\IO\Helpers::join('a/b', 'c/../d'))->toEqual('a/b/d');
      expect(\IO\Helpers::join('a/b', '..', '../x'))->toEqual('x');
      expect(\IO\Helpers::join('a/////b/.///./../x'))->toEqual('a/x');
    });

    it('tmp() should resolve properly', function() {
      $tmp = \IO\Helpers::tmp(uniqid('fixtures'));

      expect(mkdir($tmp))->toBe(TRUE);
      expect(rmdir($tmp))->toBe(TRUE);
    });
  });
});
