<?php

namespace IsaacMachakata\CodelSms;

/**
 * Constant URLS for the API
 */
class Urls
{
    /**
     * The endpoint url for bulk sms
     */
    const BASE_URL = 'https://2wcapi.codel.tech/2wc';
    const BALANCE_ENDPOINT = '/credit-balance-request/v1/api';
    const SINGLE_SMS_ENDPOINT_DEFAULT_SENDER = '/single-sms/v1/api';
    const SINGLE_SMS_ENDPOINT = '/single-sms/v2/api';
    const MULTIPLE_SMS_ENDPOINT = '/multiple-sms/v1/api';
}
