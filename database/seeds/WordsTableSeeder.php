<?php

use App\Models\Words;
use Illuminate\Database\Seeder;

class WordsTableSeeder extends Seeder
{
    public function run()
    {
        Words::create([
            'word' => 'linked list',
            'file_name' => 'linked_list.md'
        ]);

        Words::create([
            'word' => 'delegate',
            'file_name' => 'delegate.md'
        ]);
    }
}
