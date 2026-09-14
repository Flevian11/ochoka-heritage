<?php

namespace App\Services;

use InvalidArgumentException;

class PhoneNumberNormalizer
{
    public static function normalize(string $phone): string
    {
        $value = preg_replace('/[\s().-]+/', '', trim($phone));

        if ($value === null || $value === '') {
            throw new InvalidArgumentException('Phone number is required.');
        }

        if (str_starts_with($value, '07') && strlen($value) === 10) {
            $value = '+254'.substr($value, 1);
        } elseif (str_starts_with($value, '254') && strlen($value) === 12) {
            $value = '+'.$value;
        }

        if (! preg_match('/^\+[1-9][0-9]{7,14}$/', $value)) {
            throw new InvalidArgumentException('Enter a valid international phone number.');
        }

        return $value;
    }
}
