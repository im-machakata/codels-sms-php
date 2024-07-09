[![CI Test Package](https://github.com/im-machakata/codels-sms/actions/workflows/ci-test.yml/badge.svg)](https://github.com/im-machakata/codels-sms/actions/workflows/ci-test.yml)

# Codel Bulk Sms (Un-Official)
This is an **unofficial** package for the [codel](https://codel.co.zw) bulk sms (also known as [2waychat.com](https://2waychat.com)).

## How it works

This version interacts with the [Codel Sms API](https://2waychat.com) and does not provide any additional functions.

## Installation 

To install and use this library, you'll need to have composer installed. After that, you can install this library with the following composer command:

```sh
composer require im-machakata/codelsms
```

## Usage 

Initialize an sms instance as follows:

```php
use IsaacMachakata\CodelSms\Sms;
use IsaacMachakata\CodelSms\Client;

$client =  new Client($apiToken);
$sms = Sms::new('263771000000','Your message goes here...');

$response = $client->send($sms);
if($response->isOk()){
    // Sip some coffee
} else {
    // Scratch your head
}
