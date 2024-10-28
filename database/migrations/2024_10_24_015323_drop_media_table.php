<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropMediaTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('media'); // Drop the media table
    }

    public function down()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        }); // Optionally recreate it (with minimal columns) if you want to rollback
    }
}
