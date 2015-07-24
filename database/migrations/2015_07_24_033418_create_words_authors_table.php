<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWordsAuthorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('words_authors', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            // new fields
            $table->unsignedInteger('word_id');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('commit');

            $table->unique('commit');
            $table->index('word_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('words_authors');
    }
}
