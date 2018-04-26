<?php

namespace Mike42\ImagePhp\Util;

/**
 * Treat incoming string as a stream of bits
 */
class LzwDecodeBuffer
{
    protected $contents;
    protected $ptr;
    protected $len;
    const MASK = [0x00, 0x01, 0x03, 0x07, 0x0F, 0x1F, 0x3F, 0x7F, 0xFF];

    public function __construct(string $contents)
    {
        $this -> contents = array_values(unpack("C*", strrev($contents)));
        $this -> ptr = count($this -> contents) * 8 - 1;
        //print_r($this -> contents);
    }

    /**
     * @param int $readBits Number of bits to read
     */
    public function read(int $readBits)
    {
        //echo "\n read($readBits)\n";
        //echo "  pointer is " . $this -> ptr . "\n";
        $num = 0;
        $firstBit = $this -> ptr - $readBits + 1;
        $lastBit = $this -> ptr;
        if ($firstBit < 0) {
            return false;
        }
        //echo "  firstBit = $firstBit, lastBit = $lastBit\n";
        $val = 0;
        for ($i = $firstBit; $i <= $lastBit; $i++) {
            $bit = $this -> readBit($i);
            $val = ($val << 1) | $bit;
        }
        $this -> ptr -= $readBits;
        //echo "\n  returning $val\n";
        return $val;
    }

    public function readBit(int $i)
    {
        //echo "  readBit($i)\n";
        $byte = intdiv($i, 8);
        //echo "   byte is $byte\n";
        $bit = $i % 8;
        $rightIgnore = 7 - $bit;
        //echo "   bit is is $bit ($rightIgnore bits to the right to remove)\n";
        return (($this -> contents[$byte]) >> $rightIgnore) & 1;
    }
}
