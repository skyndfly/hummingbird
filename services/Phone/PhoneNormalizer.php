<?php

namespace app\services\Phone;

class PhoneNormalizer
{
    public static function normalize(string $value): string
    {
        return preg_replace('/\\D+/', '', $value) ?? '';
    }
}
