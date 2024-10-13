<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAttendanceForeignKey extends Migration
{
    public function up()
    {
        Schema::table('attendance', function (Blueprint $table) {
            // Check if the foreign key exists before trying to drop it
            if (Schema::hasColumn('attendance', 'cleaner_id')) {
                $table->dropForeign(['cleaner_id']);
            }
        });
    }

    public function down()
    {
        Schema::table('attendance', function (Blueprint $table) {
            // Re-add the foreign key if necessary
            $table->foreign('cleaner_id')->references('id')->on('cleaners');
        });
    }
}

