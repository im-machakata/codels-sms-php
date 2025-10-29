<?php

use PHPUnit\Framework\TestCase;
use IsaacMachakata\CodelSms\Client;
use PHPUnit\Framework\Attributes\DataProvider;
use IsaacMachakata\CodelSms\Exception\MalformedConfigException;

class ClientTest extends TestCase
{
    #[DataProvider('setConfigValues')]
    public function testValidConfigurations($config)
    {
        $this->assertIsObject(new Client($config));
    }
    #[DataProvider('invalidConfigValues')]
    public function testInvalidConfigurationsThrowsErrors($config)
    {
        $this->expectException(MalformedConfigException::class);
        new Client($config);
    }
    public static function setConfigValues(): array
    {
        return [
            ['a-valid-api-token-key'],
        ];
    }
    public static function invalidConfigValues(): array
    {
        return [
            [''],
            [array()],
            [array('username' => 'admin')],
            [array('username' => 'supersecret')],
            [array('username' => 'admin', 'password' => 'supersecret')],
        ];
    }
}
