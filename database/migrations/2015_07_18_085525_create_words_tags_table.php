<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWordsTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('words_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            // new fields
            $table->unsignedInteger('word_id');
            $table->string('tag');

            $table->unique(['word_id', 'tag']);
            $table->index('tag');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('words_tags');
    }
}
