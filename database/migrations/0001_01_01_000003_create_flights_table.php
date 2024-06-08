<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlightsTable extends Migration
{
    public function up()
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->string('ext_id')->unique();
            $table->boolean('one_way');
            $table->string('currency');
            $table->decimal('price', 10, 2);
            $table->integer('number_of_seats');
            $table->string('last_ticketing_date');
            $table->string('last_ticketing_date_time');
            $table->string('duration');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('flights');
    }
}


