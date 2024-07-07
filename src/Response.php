<?php

namespace IsaacMachakata\CodelSms;

use GuzzleHttp\Psr7\Response as Psr7Response;
use IsaacMachakata\CodelSms\Interface\ResponseInterface;

class Response implements ResponseInterface
{
    private Psr7Response $response;
    private object $responseBody;
    public function __construct(Psr7Response $response)
    {
        $this->response = $response;
        if ($this->response->getStatusCode() == 200) {
            $this->responseBody = json_decode($this->response->getBody());
        }
    }
    public function getCreditsUsed(): int
    {
        return !empty($this->responseBody) ? $this->responseBody->charge : 0;
    }
    public function getMessageStatus(): string
    {
        return !empty($this->responseBody) ? $this->responseBody->status : 'FAILED';
    }
    public function isOk(): bool
    {
        return $this->getMessageStatus() != "FAILED";
    }
}
