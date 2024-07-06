<?php

use PHPUnit\Framework\TestCase;
use IsaacMachakata\CodelSms\BulkSmsApi;
use IsaacMachakata\CodelSms\BulkSmsException;
use PHPUnit\Framework\Attributes\DataProvider;

class BulkSmsApiTest extends TestCase
{
    #[DataProvider('setConfigValues')]
    public function testValidConfigurations($config)
    {
        $this->assertIsObject(new BulkSmsApi($config));
    }
    #[DataProvider('setEmptyConfigValues')]
    public function testEmptyConfigurationsThrowsErrors($config)
    {
        $this->expectException(BulkSmsException::class);
        new BulkSmsApi($config);
    }
    public static function setConfigValues(): array
    {
        return [
            ['a-valid-api-token-key'],
            [array('username' => 'admin', 'password' => 'supersecret')],
        ];
    }
    public static function setEmptyConfigValues(): array
    {
        return [
            [''],
            [array()],
            [array('username' => 'admin')],
            [array('username' => 'supersecret')],
        ];
    }
}
