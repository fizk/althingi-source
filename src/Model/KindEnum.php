<?php

namespace Althingi\Model;

use Exception;

enum KindEnum: string
{
    case A = 'a';
    case B = 'b';

    public static function fromString(string $value)
    {
        return match (strtolower($value)) {
            'a' => self::A,
            'b' => self::B,
            default => throw new Exception("'$value' is not a valid string"),
        };
    }
}
