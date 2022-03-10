<?php

namespace App\Traits;

interface CachableInterface
{
    /**
     * Get the value of cacheEnabled - If model caching enabled
     * @return bool
     */
    public static function getCacheEnabled(): bool;

    /**
     * Get the value of cacheKey
     * @return string
     */
    public static function getCacheKey(): string;

    /**
     * Get the value of cacheTTL
     * @return int
     */
    public static function getCacheTTL(): int;
}
