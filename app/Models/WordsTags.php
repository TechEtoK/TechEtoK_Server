<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class WordsTags extends Model
{
    protected $fillable = ['word_id', 'tag'];

    /**
     * @param $word_id
     * @return WordsTags[]
     */
    public static function getByWord($word_id)
    {
        return static::query()->where('word_id', '=', $word_id)->get();
    }

    public static function deleteByWord($word_id)
    {
        return static::query()->where('word_id', '=', $word_id)->delete();
    }
}
