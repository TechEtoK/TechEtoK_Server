<?php

use App\Models\WordsTags;
use Illuminate\Database\Seeder;

class WordsTagsTableSeeder extends Seeder
{
    public function run()
    {
        WordsTags::create([
            'word_id' => 1,
            'tag' => 'linked'
        ]);

        WordsTags::create([
            'word_id' => 1,
            'tag' => 'list'
        ]);

        WordsTags::create([
            'word_id' => 1,
            'tag' => 'linkedlist'
        ]);
    }
}
