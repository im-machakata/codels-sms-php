<?php

namespace IsaacMachakata\CodelSms;

use IsaacMachakata\CodelSms\Constant\InvalidPhoneNumber;

class Utils
{

    public static function formatNumber(string $destination)
    {
        if (str_starts_with($destination, '+')) {
            $destination = str_replace('+', '', $destination);
        }

        if (strlen($destination) == 10) {
            $destination = str_split($destination);
            unset($destination[0]);
            $destination = implode($destination);
        }

        if (strlen($destination) == 9) {
            $destination = "263" . $destination;
        }

        if (strlen($destination) > 13 || strlen($destination) < 9) {
            throw new InvalidPhoneNumber('Phone number is invalid. Make sure it is of the format: 263...');
        }

        return $destination;
    }
}
