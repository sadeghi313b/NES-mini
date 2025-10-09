<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Active   = 'active';
    case Force    = 'force';
    case Hold     = 'hold';
    case Canceled = 'canceled';
    case Enough   = 'enough';

    // optional: helper for showing labels
    public function label(): string
    {
        return match ($this) {
            self::Active   => 'Active',
            self::Force    => 'Force',
            self::Hold     => 'Hold',
            self::Canceled => 'Canceled',
            self::Enough   => 'Enough',
            default => 'Error OrderStatus Enum',
        };
    }

    // optional: get all values
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    // optional: get as array for dropdowns
    public static function options(): array
    {
        return array_map(fn($case) => [
            'label' => $case->label(),
            'value' => $case->value,
        ], self::cases());
    }
}
