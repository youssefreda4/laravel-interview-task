<?php

if (!function_exists('enum_data')) {
    /**
     * Convert an enum instance (or null) into a structured array
     * Supports optional methods like label(), background(), textColor()
     */
    function enum_data(?UnitEnum $enum): ?array
    {
        if (!$enum) {
            return null;
        }

        $data = [
            'value' => $enum->value,
            'label' => method_exists($enum, 'label') ? $enum->label() : $enum->value,
        ];

        if (method_exists($enum, 'background')) {
            $data['background_color'] = $enum->background();
        }

        if (method_exists($enum, 'textColor')) {
            $data['text_color'] = $enum->textColor();
        }

        return $data;
    }
}
