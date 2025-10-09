<?php

namespace Database\Factories\Concerns;

trait FactoryHelper
{
    /**
     * Safely get ID from factory or return fallback
     */
    protected function safeFactoryId(string $modelClass, int $fallbackId = 1): int
    {
        return rescue(
            fn() => $modelClass::factory()->id(),
            $fallbackId,
            false
        );
    }
}
