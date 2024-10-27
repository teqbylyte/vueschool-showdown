<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ApiCall extends Model
{
    const SINGLE = 'single';
    const BATCH = 'batch';

    /**
     * To check if the api calls for single requests is within the providers limit.
     * @return bool
     */
    public static function isWithinSingleLimit() {
        return self::single()->value('count') < 3600;
    }

    public static function isWithinBatchLimit() {
        return self::batch()->value('count') < 50;
    }

    public function scopeSingle(Builder $builder)
    {
        $builder->whereType(self::SINGLE);
    }

    public function scopeBatch(Builder $builder)
    {
        $builder->whereType(self::BATCH);
    }
}
