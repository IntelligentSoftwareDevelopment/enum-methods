<?php

namespace IntelligentSoftwareDevelopment\EnumMethods\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Exception;

trait HasEnumMethods
{
    /**
     * @param ...$cases
     * @return bool
     */
    public function is(...$cases): bool
    {
        return $this->collectCases($cases)->some(fn (self $case) => $this === $case);
    }

    /**
     * @param array $cases [int|string|self] ...$args
     * @return bool
     */
    public function isNot(...$cases): bool
    {
        return $this->collectCases($cases)->every(fn (self $case) => $this !== $case);
    }

    /**
     * @throws Exception
     */
    public function __call(string $method, array $args)
    {
        if (!Str::startsWith($method, 'is')) {
            throw new Exception("Undefined method {$method} on enum " . self::class);
        }

        return $this === self::try(Str::replace('is', '', $method));
    }

    /**
     * @param array $cases
     * @return Collection<int, self>
     */
    private function collectCases(array $cases): Collection
    {
        return collect($cases)
            ->flatten()
            ->map(fn ($case) => $case instanceof self ? $case : self::tryFrom($case))
            ->filter();
    }

    private static function try(string $case): ?self
    {
        return self::tryFrom($case)
            ?? self::tryFrom(lcfirst($case))
            ?? self::tryFrom(ucfirst($case))
            ?? self::tryFromKebab($case)
            ?? self::tryFromSnake($case)
            ?? self::tryFromSpaced($case);
    }

    private static function tryFromKebab(string $case): ?self
    {
        return self::tryFrom(Str::kebab($case)) ?? self::tryFrom(Str::upper(Str::kebab($case)));
    }

    private static function tryFromSnake(string $case): ?self
    {
        return self::tryFrom(Str::snake($case)) ?? self::tryFrom(Str::upper(Str::snake($case)));
    }

    private static function tryFromSpaced(string $case): ?self
    {
        return self::tryFrom(Str::snake($case, ' '))
            ?? self::tryFrom(Str::upper(Str::snake($case, ' ')));
    }
}
