<?php

namespace App\Enums;

enum Timezone: string {
    case CET = 'CET';
    case CST = 'CST';
    case GMT = 'GMT+1';

    public static function values()
    {
        return array_column(self::cases(), 'value');
    }
}
