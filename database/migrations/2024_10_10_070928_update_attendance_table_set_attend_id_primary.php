<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAttendanceTableSetAttendIdPrimary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance', function (Blueprint $table) {
            // Drop the existing id column
            $table->dropColumn('id'); // Be careful: this will lead to data loss in the id column

            // Add attend_id column as an auto-incrementing primary key
            $table->increments('attend_id')->first(); // This makes it the first column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance', function (Blueprint $table) {
            // Drop the attend_id column if you need to revert the migration
            $table->dropColumn('attend_id');

            // Optionally, you can add the id column back
            $table->increments('id'); // Uncomment if needed to restore id
        });
    }
}


