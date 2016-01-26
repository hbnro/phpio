<?php

describe('IO', function() {
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
