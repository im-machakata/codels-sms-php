# Codel Bulk Sms (Un-Official)
This is an **unofficial** package for the [codel](codel.co.zw) bulk sms (also known as [2waychat.com](2waychat.com)). Efforts were made to add as many features as possible, something the original api lacked in my opinion.

## How it works

When you pass in your Codel Bulk Sms API token, the library will basically interact with their API. However when you give it your username and password, it'll scrape through the site and provide you with information from the server. The latter is slower but offers more options.

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

// get response
$response = $client->send($sms);
if($response->isOk()){
    // Sip some coffee
} else {
    // Scratch your head
}