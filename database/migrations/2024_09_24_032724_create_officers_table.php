<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficersTable extends Migration
{
    public function up()
    {
        Schema::create('officers', function (Blueprint $table) {
            $table->id(); // Primary key 'id' which is an unsignedBigInteger
            $table->string('officer_email')->unique();
            $table->string('officer_pass');
            $table->string('officer_name');
            $table->string('officer_phoneNo');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('officers');
    }
}
