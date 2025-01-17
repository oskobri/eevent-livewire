<?php

namespace App\Enums;

trait Enumerable
{
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function collection(): array
    {
        return array_combine(self::values(), self::names());
    }
}
