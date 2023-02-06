<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('realty_offers', function (Blueprint $table) {
            $table->id();
            $table->text('url')->nullable();
            $table->string('offerId');
            $table->string('title');
            $table->longText('description')->nullable();
            $table->text('address')->nullable();
            $table->float('area')->nullable();
            $table->string('price')->nullable();
            $table->integer('roomsTotal')->nullable();
            $table->string('dealStatus')->nullable();
            $table->string('builtYear')->nullable();
            $table->string('image')->nullable();
            $table->timestamp('date')->nullable();
            $table->float('latitude')->nullable();
            $table->float('longitude')->nullable();
            $table->string('from');
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
        Schema::dropIfExists('realty_offers');
    }
};
