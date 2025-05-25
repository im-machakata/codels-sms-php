<?php

namespace IsaacMachakata\CodelSms;

/**
 * Represents an sms instance for the codel bulk sms service.
 * @author Isaac Machakata <hie@isaac.co.zw>
 */

class Sms
{
    private static string $destination;
    private static string $message;
    private static ?string $reference = null;
    private static ?string $timestamp = null;
    private static ?string $validity = '03:00';

    /**
     * Creates a new message instance and returns the back the data in an array formatted for the API.
     *
     * @param string $destination
     * @param string $message
     * @param string|null $reference
     * @param string|null $timestamp
     * @param string $validity
     * @return self
     */
    public static function new(string $destination, string $message, ?string $reference = null, ?string $timestamp = null, ?string $validity = '03:00'): self
    {
        // make sure optional variables are populated
        if (empty($timestamp)) $timestamp = time();
        if (empty($reference)) $reference = uniqid();

        // set values to the properties
        self::$destination = Utils::formatNumber($destination);
        self::$timestamp = $timestamp;
        self::$reference = $reference;
        self::$validity = $validity;
        self::$message = $message;

        // return instance
        return new Sms;
    }

    /**
     * Prepares the variables into an array
     *
     * @return array
     */
    public static function toArray()
    {
        return [
            'destination' => self::$destination,
            'messageText' => self::$message,
            'messageReference' => self::$reference,
            'messageDate' => date('YmdHis'),
            'messageValidity' => self::$validity,
            'sendDateTime' => date('H:i', self::$timestamp),
        ];
    }
}
