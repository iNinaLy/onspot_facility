<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCleanersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cleaners', function (Blueprint $table) {
            $table->id('cleaner_id'); // Primary key
            $table->string('cleaner_name'); // Cleaner name
            $table->string('cleaner_phoneNo'); // Cleaner phone number
            $table->boolean('cleaner_available')->default(true); // Cleaner availability status
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
        Schema::dropIfExists('cleaners');
    }
}
