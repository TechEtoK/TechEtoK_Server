<?php

use App\Models\WordsAuthors;
use Illuminate\Database\Seeder;

class WordsAuthorsTableSeeder extends Seeder
{
    public function run()
    {
        WordsAuthors::create([
            'word_id' => 1,
            'name' => '유대열',
            'email' => 'dy@yoobato.com',
            'commit' => '9e30d2a'
        ]);

        WordsAuthors::create([
            'word_id' => 2,
            'name' => '유대열',
            'email' => 'dy@yoobato.com',
            'commit' => 'd04cce7'
        ]);

        WordsAuthors::create([
            'word_id' => 1,
            'name' => '유대열',
            'email' => 'dy@yoobato.com',
            'commit' => '4287218'
        ]);

        WordsAuthors::create([
            'word_id' => 2,
            'name' => '유대열',
            'email' => 'dy@yoobato.com',
            'commit' => '2b98d98'
        ]);

        WordsAuthors::create([
            'word_id' => 1,
            'name' => '유대열',
            'email' => 'dy@yoobato.com',
            'commit' => '19f06a8'
        ]);
    }
}
