<?php

namespace IsaacMachakata\CodelSms;

use GuzzleHttp\Client as GuzzleClient;
use IsaacMachakata\CodelSms\Exception\MalformedConfigException;

/**
 * Allows you to send sms's from your PHP app.
 * @throws MalformedConfigException
 * @final
 */
final class Client //implements ClientInterface
{
    private GuzzleClient $client;
    private array|string $config;
    protected string $receivers;
    protected string $senderID = '';
    protected $templateCallback;
    protected Sms $message;

    /**
     * @param string|array $config
     */
    function __construct($config = null, ?string $senderID = null)
    {
        $this->config = $config;
        $this->client = new GuzzleClient();
        $this->processConfigurations();

        // set sender id if not empty
        if (!is_null($senderID)) {
            $this->setSenderId($senderID);
        }
    }

    public function setSenderId(string $senderID)
    {
        $this->senderID = $senderID;
        return $this;
    }

    /**
     * @param callable $templateCallback
     */
    public function setCallback(callable $templateCallback)
    {
        $this->templateCallback = $templateCallback;
        return $this;
    }

    /**
     * Makes a request to the server and tries to send the message.
     *
     * @return Response
     */
    public function send(string|array $receivers, array|string $messages): Response
    {
        return $this->sendMessages($receivers, $messages);
    }

    /**
     * Gets the current credit balance for the account.
     * @return int|object
     */
    public function getBalance()
    {
        $uri = Urls::BASE_URL . Urls::BALANCE_ENDPOINT;
        $response = $this->client->request('post', $uri, [
            'headers' => [
                'Accept' => 'application/json'
            ],
            'json' => [
                'token' => $this->config,
            ],
        ]);

        if ($response->getStatusCode() == 200) {
            return (int) json_decode($response->getBody())->sms_credit_balance;
        }
        return json_decode($response->getBody());
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

        if (!$this->configIsToken()) {
            throw new \Exception('Method not yet supported! Please use API Token.');
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

    private function sendMessages(string|array $receivers, string|array $messages)
    {
        if (empty($messages)) {
            throw new \Exception('Message can not be empty.');
        }

        if (is_string($receivers)) {
            return $this->sendSingleMessage($receivers, $messages);
        }

        // check if we're sending one message to multiple users
        // or different messages to different users
        if (is_array($receivers) && is_array($messages)) {
            if (count($receivers) != count($messages)) {
                throw new \Exception('Number of receivers and messages do not match.');
            }
        }

        // send bulk sms
        return $this->sendBulkMessages($receivers, $messages);
    }

    /**
     * Processes configurations and sends a single message
     *
     * @param string $receiver
     * @param string $message
     * @throws \Exception
     * @return Response
     */
    private function sendSingleMessage(string $receiver, string $message)
    {
        if (empty($message)) {
            throw new \Exception('Message can not be empty.');
        }

        $requestJson = [
            ...Sms::new($receiver, $message)->toArray(),
            'token' => $this->config,
        ];
        if (!empty($this->senderID)) {
            $requestJson['sender_id'] = $this->senderID;
        }
        $uri = Urls::BASE_URL . Urls::SINGLE_SMS_ENDPOINT;
        $response = $this->client->request('post', $uri, [
            'headers' => [
                'Accept' => 'application/json'
            ],
            'json' => $requestJson,
        ]);
        return new Response($response);
    }
    /**
     * Processes configurations and sends a single message
     *
     * @param Sms $sms
     * @throws \Exception
     * @return Response
     */
    private function sendBulkMessages(string|array $messages)
    {
        if (empty($sms)) {
            throw new \Exception('Message can not be empty.');
        }

        $requestJson = [
            ...$sms->toArray(),
            'token' => $this->config,
        ];
        if ($this->senderID) $requestJson['sender_id'] = $this->senderID;
        $uri = Urls::BASE_URL . Urls::SINGLE_SMS_ENDPOINT;
        $response = $this->client->request('post', $uri, [
            'headers' => [
                'Accept' => 'application/json'
            ],
            'json' => $requestJson,
        ]);
        return new Response($response);
    }
}
