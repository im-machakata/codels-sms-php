<?php

use PHPUnit\Framework\TestCase;
use IsaacMachakata\CodelSms\Sms;
use PHPUnit\Framework\Attributes\DataProvider;

class SmsTest extends TestCase
{
    public function testSmsIsArray()
    {
        $sms = Sms::new("263771000000", "Hello world!", '#ref1');
        $this->assertIsArray($sms);
    }
    #[DataProvider('addTestArrayKeys')]
    public function testSmsHasKey(string $key)
    {
        $sms = Sms::new("263771000000", "Hello world!");
        $this->assertArrayHasKey($key, $sms);
    }
    public static function addTestArrayKeys(): array
    {
        return [
            ['destination'],
            ['messageText'],
            ['messageDate'],
            ['sendDateTime'],
            ['messageValidity'],
            ['messageReference'],
        ];
    }
}
