<?php

namespace IsaacMachakata\CodelSms\Interface;

use IsaacMachakata\CodelSms\Sms;

interface ClientInterface
{
    public function send(Sms $sms): ResponseInterface;
}
