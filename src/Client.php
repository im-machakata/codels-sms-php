<?php

namespace IsaacMachakata\CodelSms;

use GuzzleHttp\Pool;
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
     * @return array
     */
    public function send(Sms|array $sms): array
    {
        return $this->sendMessages($sms);
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
     * @param Sms|array<Sms> $sms
     * @throws \Exception
     * @return array
     */
    private function sendMessages(Sms|array $sms, callable $callback = null)
    {
        if (!$this->configIsToken()) {
            throw new \Exception('Method not yet supported! Please use API Token.');
        }
        $successfulSends = 0;
        $failedSends = 0;
        $allResponses = [];
        $uri = Urls::BASE_URL . Urls::SINGLE_SMS_ENDPOINT;
        if (!is_array($sms)) {
            $sms = [$sms];
        }

        if (empty($sms)) {
            throw new \Exception('Message can not be empty.');
        }

        $requests = function ($total) use ($sms, $uri) {
            foreach ($sms as $message) {
                yield $this->client->requestAsync('POST', $uri, [
                    // 'headers' => [],
                    'json' => [
                        ...$message->toArray(),
                        'token' => $this->config,
                    ],
                ]);
            }
        };


        $pool = new Pool($this->client, $requests(count($sms)), [
            'concurrency' => 4,
            'fulfilled' => function (GuzzleResponse $response, $index) use ($sms, $callback, &$successfulSends, &$failedSends, &$allResponses) {
                // Type hint $response
                $message = $sms[$index];
                $body = $response->getBody();
                $responseData = json_decode($body, true);

                if (is_callable($callback)) {
                    $callback($message, $responseData, null);
                }
                $allResponses[] = ['message' => $message, 'response' => $responseData, 'status' => 'success'];
                // Store the data

                if (is_callable($callback)) {
                    $callback($message, $responseData, null);
                }
        
                $successfulSends++;
            },
            'rejected' => function ($reason, $index) use ($sms, $callback, &$successfulSends, &$failedSends, &$allResponses) {
                $message = $sms[$index];
                $errorMessage = $reason->getMessage();

                if (is_callable($callback)) {
                    $callback($message, null, $errorMessage);
                }
                $allResponses[] = ['message' => $message, 'error' => $errorMessage, 'status' => 'failed']; // Store the data

                if (is_callable($callback)) {
                    $callback($message, null, $errorMessage);
                }
        
                $failedSends++;
            },
        ]);

        $promise = $pool->promise();
        $promise->wait();
        
        $totalSends = count($sms);
        $overallStatus = ($failedSends === 0) ? 'success' : 'partial'; // Or 'failed' if all failed
        
        return [
            'status' => $overallStatus,
            'total_messages' => $totalSends,
            'successful_messages' => $successfulSends,
            'failed_messages' => $failedSends,
            'responses' => $allResponses, // Include details of each message
        ];
    }
}
