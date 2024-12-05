<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateCleanerIdInAttendance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance', function (Blueprint $table) {
            // Check if the foreign key exists before attempting to drop it
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_NAME = 'attendance' AND COLUMN_NAME = 'cleaner_id' AND CONSTRAINT_SCHEMA = DATABASE();
            ");
            
            if (!empty($foreignKeys)) {
                $table->dropForeign(['cleaner_id']); // Drop existing foreign key
            }

            // Add the new foreign key referencing cleaners.user_id
            $table->foreign('cleaner_id')
                ->references('user_id')->on('cleaners')
                ->onDelete('cascade');
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
            // Check if the foreign key exists before attempting to drop it
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_NAME = 'attendance' AND COLUMN_NAME = 'cleaner_id' AND CONSTRAINT_SCHEMA = DATABASE();
            ");

            if (!empty($foreignKeys)) {
                $table->dropForeign(['cleaner_id']);
            }

            // Restore the original foreign key referencing cleaners.id
            $table->foreign('cleaner_id')
                ->references('id')->on('cleaners')
                ->onDelete('cascade');
        });
    }
}
