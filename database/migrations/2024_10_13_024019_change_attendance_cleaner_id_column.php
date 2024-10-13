<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeAttendanceCleanerIdColumn extends Migration
{
    public function up()
    {
        Schema::table('attendance', function (Blueprint $table) {
            // Drop the existing cleaner_id column if it exists
            if (Schema::hasColumn('attendance', 'cleaner_id')) {
                $table->dropColumn('cleaner_id');
            }

            // Add a new cleaner_id column as a foreign key referencing the cleaners table
            $table->unsignedBigInteger('cleaner_id'); // Adjust if necessary

            // Create the foreign key constraint
            $table->foreign('cleaner_id')->references('id')->on('cleaners')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('attendance', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['cleaner_id']);
            // Drop the new cleaner_id column
            $table->dropColumn('cleaner_id');
        });
    }
}
