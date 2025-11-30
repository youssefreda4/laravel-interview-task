<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending  = 'pending';
    case Paid     = 'paid';
    case Cancelled  = 'cancelled';

    /**
     * Get labels for each status
     */
    public static function labels(): array
    {
        return [
            self::Pending->value  => __('Pending'),
            self::Paid->value     => __('Paid'),
            self::Cancelled->value  => __('Cancelled'),
        ];
    }

    /**
     * Get all values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get the label of current status
     */
    public function label(): string
    {
        return self::labels()[$this->value];
    }
}
