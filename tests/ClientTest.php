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
    #[DataProvider('setEmptyConfigValues')]
    public function testEmptyConfigurationsThrowsErrors($config)
    {
        $this->expectException(MalformedConfigException::class);
        new Client($config);
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
