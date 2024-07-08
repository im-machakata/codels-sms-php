<?php

namespace IsaacMachakata\CodelSms\Interface;

interface ResponseInterface
{
    public function isOk(): bool;
    public function getCreditsUsed(): int;
    public function messageIsScheduled(): bool;
    public function getMessageId(): string|null;
    public function getMessageStatus(): string;
}
