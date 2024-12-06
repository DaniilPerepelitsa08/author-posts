<?php

namespace App\Enums;

/**
 * Enum PostStatusesEnum
 *
 * Перечисление статусов постов.
 *
 * @package App\Enums
 */
enum PostStatusesEnum: int
{
    /**
     * Post status: Private.
     *
     * @var int
     */
    case PRIVATE = 1;

    /**
     * Post status: Public.
     *
     * @var int
     */
    case PUBLIC = 0;

    /**
     * Get a list of all case names in the enum.
     *
     * @return string[] Array of case names.
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Get a list of all case values in the enum.
     *
     * @return int[] Array of case values.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get an associative array of case values and names.
     *
     * Keys are values, and values are names.
     *
     * @return array<int, string> Associative array of cases.
     */
    public static function array(): array
    {
        return array_combine(self::values(), self::names());
    }
}
