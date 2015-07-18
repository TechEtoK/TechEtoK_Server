<?php

use App\Models\Words;
use Illuminate\Database\Seeder;

class WordsTableSeeder extends Seeder
{
    public function run()
    {
        Words::create([
            'word' => 'linked list',
            'filename' => 'linked_list.md'
        ]);

        Words::create([
            'word' => 'delegate',
            'filename' => 'delegate.md'
        ]);
    }
}
