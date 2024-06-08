<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSegmentsTable extends Migration
{
    public function up()
    {
        Schema::create('segments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('flight_id');
            $table->string('carrier_code');
            $table->string('flight_number');
            $table->string('departure_iata');
            $table->string('arrival_iata');
            $table->dateTime('departure_time')
                ->index('departure_time_idx');
            $table->dateTime('arrival_time')
                ->index('arrival_time_idx');
            $table->integer('number_of_stops');
            $table->string('duration');
            $table->timestamps();

            $table->foreign('flight_id')
                ->references('id')
                ->on('flights')
                ->onDelete('cascade');

            $table->unique([
                'carrier_code',
                'flight_number',
                'departure_time',
                'arrival_time',
            ], 'unique_segment');
        });
    }

    public function down()
    {
        Schema::dropIfExists('segments');
    }
}

