<?php

namespace app\config;
class EnvRegistry
{
    public static function getOwnerLogin(): string
    {
        return getenv("OWNER_LOGIN");
    }

    public static function getOwnerPassword(): string
    {
        return getenv("OWNER_LOGIN");
    }

    public static function getOwnerFirstName(): string
    {
        return getenv("OWNER_FIRST_NAME");
    }

    public static function getOwnerName(): string
    {
        return getenv("OWNER_NAME");
    }

    public static function getOwnerLastName(): string
    {
        return getenv("OWNER_NAME_LAST_NAME");
    }

    public static function getOwnerPhoneNumber(): string
    {
        return getenv("OWNER_PHONE_NUMBER");
    }
}