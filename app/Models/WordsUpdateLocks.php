<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class WordsUpdateLocks extends Model
{
    public static function isLocked() {
        return static::query()->get()->first()->locked == 1;
    }

    public static function setLock($lock) {
        return static::query()->update(['locked' => $lock]);
    }
}
