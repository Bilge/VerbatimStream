<?php
use Bilge\Stream\VerbatimStream;

class VerbatimTest extends PHPUnit_Framework_TestCase {
    use VerbatimStream;

    const
        PROTOCOL = 'verbatim://',
        GIF = "GIF89a\x02\x00\x01\x00\xF0"
    ;

    public function testFileGetContents() {
        $this->assertSame('foobar', file_get_contents(self::PROTOCOL . 'foobar'));
    }

    public function testFread() {
        $h = fopen(self::PROTOCOL . 'fubar', 'rb');

        $this->assertSame('fubar', fread($h, 1024));

        fclose($h);
    }

    public function testStat() {
        $h = fopen(self::PROTOCOL . '1234567890', 'rb');

        $this->assertSame(10, fstat($h)['size']);

        fclose($h);
    }

    public function testUrlStat() {
        $this->assertSame(10, filesize(self::PROTOCOL . '1234567890'));
    }

    public function testReadable() {
        $this->assertTrue(is_readable(self::PROTOCOL));
    }

    public function testFile() {
        $this->assertTrue(is_file(self::PROTOCOL));
    }

    public function testNotWritable() {
        $this->assertFalse(is_writable(self::PROTOCOL));
    }

    public function testNotDirectory() {
        $this->assertFalse(is_dir(self::PROTOCOL));
    }

    /** @depends testFileGetContents */
    public function testEmpty() {
        $this->assertEmpty(file_get_contents(self::PROTOCOL));
    }

    /**
     * @depends testFileGetContents
     */
    public function testBigData() {
        $hex = '0123456789abcdef';
        $times = 0x10000;
        $data1MiB = str_repeat($hex, $times);

        $this->assertSame($data1MiB, file_get_contents(self::PROTOCOL . $data1MiB));
    }

    /**
     * @depends testFileGetContents
     */
    public function testNull() {
        $this->assertSame(chr(0), file_get_contents(self::PROTOCOL . rawurlencode("\000")));
    }

    /**
     * @dataProvider provideChar
     * @depends testNull
     */
    public function testEachChar($char) {
        $this->assertSame($char, file_get_contents(self::PROTOCOL . rawurlencode($char)), ord($char));
    }

    /**
     * @dataProvider provideAllChars
     * @depends testNull
     */
    public function testAllChars($chars) {
        $this->assertSame($chars, file_get_contents(self::PROTOCOL . rawurlencode($chars)));
    }

    /**
     * @depends testNull
     */
    public function testGetImageSize() {
        list($width, $height) = getimagesize(self::PROTOCOL . rawurlencode(self::GIF));

        $this->assertSame(2, $width);
        $this->assertSame(1, $height);
    }

    public function provideChar() {
        for ($i = 0; $i <= 0xFF; ++$i)
            yield [chr($i)];
    }

    public function provideAllChars() {
        return [[array_reduce(iterator_to_array($this->provideChar()), function($s, $v) { return "$s$v[0]"; })]];
    }
}