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
            $table->id();
            $table->string('cleaner_name');
            $table->string('cleaner_phoneNo');
            $table->string('username')->unique(); // Username must be unique and not nullable
            $table->string('password');
            $table->string('status')->default('active'); // Setting default value for status
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
        Schema::dropIfExists('cleaners'); // Drop the cleaners table if it exists
    }
}
