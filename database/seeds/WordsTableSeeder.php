<?php

use App\Models\Words;
use Illuminate\Database\Seeder;

class WordsTableSeeder extends Seeder
{
    public function run()
    {
        Words::create([
            'word' => 'Linked List',
            'file_name' => 'linked_list.md'
        ]);

        Words::create([
            'word' => 'Delegate',
            'file_name' => 'delegate.md'
        ]);
    }
}
