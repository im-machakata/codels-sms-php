<?php

namespace IsaacMachakata\CodelSms\Interface;

use IsaacMachakata\CodelSms\Sms;

interface ClientInterface
{
    /**
     * @param Sms|array<Sms> $sms
     */
    public function send(Sms|array $sms): ResponseInterface;
}
