<?php

namespace IsaacMachakata\CodelSms;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use IsaacMachakata\CodelSms\Interface\ClientInterface;
use IsaacMachakata\CodelSms\Interface\ResponseInterface;
use IsaacMachakata\CodelSms\Exception\MalformedConfigException;

/**
 * Allows you to send sms's from your PHP app.
 * @throws MalformedConfigException
 * @method function send(Sms $sms)
 * @final
 */
final class Client implements ClientInterface
{
    private GuzzleClient $client;
    private array|string $config;
    protected string $receivers;
    protected string $senderID;
    protected Sms $message;

    /**
     * @param string|array $config
     */
    function __construct($config = null)
    {
        $this->config = $config;
        $this->client = new GuzzleClient();
        $this->processConfigurations();
    }

    /**
     * Makes a request to the server and tries to send the message.
     *
     * @return Response
     */
    public function send(Sms $sms): ResponseInterface
    {
        $response = $this->sendSingleMessage($sms);
        return new Response($response);
    }

    /**
     * Checks if provided configurations matche the expected format
     * @throws MalformedConfigException
     * @return void
     */
    private function processConfigurations()
    {
        if (!is_array($this->config) && !is_string($this->config)) {
            throw new MalformedConfigException("Invalid configurations: Config should be an API Key or an array with username & password");
        }

        if (is_array($this->config)) {
            if (!isset($this->config['username']) || empty($this->config['username'])) {
                throw new MalformedConfigException("Username can not be empty");
            }
            if (!isset($this->config['password']) || empty($this->config['password'])) {
                throw new MalformedConfigException("Password can not be empty");
            }
        }

        if (is_string($this->config) && empty($this->config)) {
            throw new MalformedConfigException('API Key can not be empty.');
        }
    }

    /**
     * Checks if the user provided an API Token or login details
     *
     * @return bool
     */
    private function configIsToken(): bool
    {
        return is_string($this->config);
    }

    /**
     * Processes configurations and sends a single message
     *
     * @param Sms $sms
     * @throws \Exception
     * @return GuzzleResponse
     */
    private function sendSingleMessage(Sms $sms)
    {
        if (!$this->configIsToken()) {
            throw new \Exception('Method not yet supported! Please use API Token.');
        }

        $uri = Urls::BASE_URL . Urls::SINGLE_SMS_ENDPOINT;
        return $this->client->request('POST', $uri, [
            // "headers" => [],
            'json' => [
                ...$sms->toArray(),
                'token' => $this->config
            ]
        ]);
    }
}
