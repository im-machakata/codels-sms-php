<?php

use PHPUnit\Framework\TestCase;
use IsaacMachakata\CodelSms\Client;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use IsaacMachakata\CodelSms\Exception\MalformedConfigException;
use PHPUnit\Framework\Attributes\DataProvider;

class ClientTest extends TestCase
{
    private Client $client;
    private MockHandler $mockHandler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $mockGuzzleClient = new GuzzleClient(['handler' => $handlerStack]);

        $this->client = new Client('a-valid-api-token-key');

        // Use reflection to replace the Guzzle client with our mock
        $reflection = new ReflectionClass($this->client);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($this->client, $mockGuzzleClient);
    }

    public function testInvalidConfigurationsThrowsErrors()
    {
        $this->expectException(MalformedConfigException::class);
        new Client('');
        new Client();
    }

    public function testGetBalance()
    {
        // Queue a mock response
        $this->mockHandler->append(new Response(200, [], json_encode(['sms_credit_balance' => 500])));

        $balance = $this->client->getBalance();
        $this->assertIsInt($balance);
        $this->assertEquals(500, $balance);
    }

    public function testSendSingleMessage()
    {
        // Queue a mock response
        $this->mockHandler->append(new Response(200, [], json_encode(['status' => 'success'])));
        $response = $this->client->send('263771000001', 'Test message');
        $this->assertInstanceOf(\IsaacMachakata\CodelSms\Response::class, $response);
        $this->assertTrue($response->isOk());

        $this->mockHandler->append(new Response(200, [], json_encode(['status' => 'failed'])));
        $response = $this->client->send('263771000001', 'Test message');
        $this->assertInstanceOf(\IsaacMachakata\CodelSms\Response::class, $response);
        $this->assertFalse($response->isOk());
    }

    public function testSendBulkMessages()
    {
        // Queue a mock response
        $fakeResponse = [
            ['status' => [
                'error_status' => 'success'
            ]]
        ];

        $this->mockHandler->append(new Response(200, [], json_encode($fakeResponse)));
        $response = $this->client->send([
            '263771000001',
            '263772000002',
        ], 'Test message');
        $this->assertInstanceOf(\IsaacMachakata\CodelSms\Response::class, $response);
        $this->assertTrue($response->isOk());

        $this->mockHandler->append(new Response(200, [], json_encode($fakeResponse)));
        $response = $this->client->send('263771000001,263771000002', 'Test message');
        $this->assertInstanceOf(\IsaacMachakata\CodelSms\Response::class, $response);
        $this->assertTrue($response->isOk());

        $fakeResponse[0]['status']['error_status'] = "failed";
        $this->mockHandler->append(new Response(200, [], json_encode($fakeResponse)));
        $response = $this->client->send('263771000001,', 'Test message');
        $this->assertInstanceOf(\IsaacMachakata\CodelSms\Response::class, $response);
        $this->assertFalse($response->isOk());
    }

    public function testSendThrowsExceptionWithMismatchedReceiversAndMessages()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Number of receivers and messages do not match.');

        $this->client->send(['263771000001'], ['message1', 'message2']);
    }

    public function testSendThrowsExceptionWithEmptyMessage()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Message(s) can not be empty.');

        $this->client->send(['263771000001'], '');
    }
}
