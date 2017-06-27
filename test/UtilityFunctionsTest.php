<?php
/**
 * Created by PhpStorm.
 * User: vbtwo
 * Date: 6/27/17
 * Time: 15:49
 */

use PHPUnit\Framework\TestCase;

class UtilityFunctionsTest extends TestCase
{

    public function testStartsWith()
    {
        $starts = starts_with('testing testing 123', 'testing');
        $this->assertTrue($starts);

        $doesntStart = starts_with('testing testing 123', '123');
        $this->assertFalse($doesntStart);
    }

    public function testTrimLeadingString()
    {
        $trimmed = trim_leading_string('testing testing 123', 'testing ');
        $this->assertEquals('testing 123', $trimmed);
    }
}
