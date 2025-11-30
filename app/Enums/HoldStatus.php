<?php

namespace App\Enums;

enum HoldStatus: string
{
    case Pending  = 'pending';
    case Used     = 'used';
    case Expired  = 'expired';
    case Released = 'released';

    /**
     * Get labels for each status
     */
    public static function labels(): array
    {
        return [
            self::Pending->value  => __('Pending'),
            self::Used->value     => __('Used'),
            self::Expired->value  => __('Expired'),
            self::Released->value => __('Released'),
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
