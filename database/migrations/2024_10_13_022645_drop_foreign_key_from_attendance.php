<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropForeignKeyFromAttendance extends Migration
{
    public function up()
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropForeign(['cleaner_id']); // Drop foreign key constraint
        });
    }

    public function down()
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->foreign('cleaner_id')->references('id')->on('cleaners')->onDelete('cascade'); // Restore foreign key
        });
    }
}
