<?php

use App\Models\WordsUpdateLocks;
use Illuminate\Database\Seeder;

class WordsUpdateLocksTableSeeder extends Seeder
{
    public function run()
    {
        WordsUpdateLocks::create([
            'locked' => false
        ]);
    }
}
