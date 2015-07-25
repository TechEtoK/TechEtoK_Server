<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class WordsAuthors extends Model
{
    protected $fillable = ['word_id', 'name', 'email', 'commit'];
}
