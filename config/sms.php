<?php

return [
    'jbs' => [
        'endpoint' => env('JBS_SMS_ENDPOINT', 'https://sms.jbs.co.ke/api/v1/'),
        'api_key' => env('JBS_SMS_API_KEY'),
        'timeout' => (int) env('JBS_SMS_TIMEOUT', 15),
        'from_name' => env('JBS_SMS_FROM_NAME', 'Ochoka Heritage'),
    ],
];
