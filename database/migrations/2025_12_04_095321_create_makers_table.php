<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMakersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('markers', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('tipoEntreno');
    $table->decimal('lat', 10, 7);
    $table->decimal('lng', 10, 7);
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->decimal('kilometros');
    $table->decimal('tiempo');
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
        Schema::dropIfExists('markers');
    }
}
