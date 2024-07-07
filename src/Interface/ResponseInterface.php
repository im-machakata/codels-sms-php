<?php

namespace IsaacMachakata\CodelSms\Interface;

interface ResponseInterface
{
    public function isOk(): bool;
    public function getCreditsUsed(): int;
    // public function getMessageId(): string;
    // public function messageScheduled(): bool;
    public function getMessageStatus(): string;
}
