<?php

namespace IntelligentSoftwareDevelopment\EnumMethods\Support;

trait HasTryFromName
{
    public static function tryFromName(string $name): ?static
    {
        $hasCase = null;
        foreach (static::cases() as $case) {
            if ($name == $case->name) {
                $hasCase = $case;
            }
        }

        return $hasCase;
    }
}
