<?php
namespace App\Enums;

enum Gender: string
{
    case Male = 'male';
    case Female = 'female';

    public static function options(): array
    {
        return [
            ['label' => 'Male', 'value' => self::Male->value],
            ['label' => 'Female', 'value' => self::Female->value],
        ];
    }
}
