<?php

use PHPUnit\Framework\TestCase;

class ChecksheetTableButtonTest extends TestCase
{
    public function testButtonDoesNotAppearBefore30th()
    {
        $date = new DateTime('2023-01-29');
        $this->assertFalse($this->isButtonVisible($date));

        $date = new DateTime('2023-01-30');
        $this->assertTrue($this->isButtonVisible($date));

        $date = new DateTime('2023-01-31');
        $this->assertTrue($this->isButtonVisible($date));
    }

    private function isButtonVisible(DateTime $date)
    {
        return $date->format('d') >= 30;
    }
}