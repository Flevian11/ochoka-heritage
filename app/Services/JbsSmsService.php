<?php

namespace App\Services;

use RuntimeException;

class JbsSmsService
{
    public function send(string|array $phoneNumbers, string $message): void
    {
        $numbers = is_array($phoneNumbers) ? $phoneNumbers : [$phoneNumbers];
        $numbers = array_values(array_filter(array_map(
            static fn (string $phone): string => PhoneNumberNormalizer::normalize($phone),
            $numbers,
        )));

        if ($numbers === []) {
            throw new RuntimeException('No valid phone numbers were supplied.');
        }

        $apiKey = (string) config('sms.jbs.api_key');
        if ($apiKey === '') {
            throw new RuntimeException('JBS SMS API key is not configured.');
        }

        $post = [
          "key" => $apiKey,
          "tel" => implode(',', $numbers),
          "msg" => $message
        ];

        $ch = curl_init(config('sms.jbs.endpoint', 'https://sms.jbs.co.ke/api/v1/'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_TIMEOUT, (int) config('sms.jbs.timeout', 15));

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $error !== '' || $httpCode >= 400) {
            throw new RuntimeException('JBS SMS delivery failed.'.($error !== '' ? ' '.$error : ''));
        }
    }
}
