<?php

namespace App\Enum;

enum Role: string
{
    case CLIENT = 'client';
    case PROVIDER = 'provider';
    case ADMIN = 'admin';

    public static function allExceptAdmin(): array
    {
        return array_filter(
            self::cases(),
            fn (self $role) => $role !== self::ADMIN
        );
    }

    public static function valuesExceptAdmin(): array
    {
        return array_map(
            fn (self $role) => $role->value,
            self::allExceptAdmin()
        );
    }
}
