<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('card_collections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_card');
            $table->unsignedBigInteger('id_collection');
            $table->foreign('id_card')->references('id')->on('cards');
            $table->foreign('id_collection')->references('id')->on('collections');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('card__collections');
    }
}
