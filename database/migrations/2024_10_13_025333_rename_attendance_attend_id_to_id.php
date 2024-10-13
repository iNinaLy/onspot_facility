<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameAttendanceAttendIdToId extends Migration
{
    public function up()
    {
        Schema::table('attendance', function (Blueprint $table) {
            // If id column already exists, just set it as primary key
            if (Schema::hasColumn('attendance', 'id')) {
                // Drop the existing primary key if it exists
                $table->dropPrimary(['attend_id']); // Adjust this if needed
                
                // Set the 'id' column as primary key
                $table->primary('id');
            }
        });
    }

    public function down()
    {
        Schema::table('attendance', function (Blueprint $table) {
            // Drop primary key from id if it exists
            if (Schema::hasColumn('attendance', 'id')) {
                $table->dropPrimary(['id']);
                
                // Optionally, you can rename back to attend_id if needed
                $table->renameColumn('id', 'attend_id');
                $table->bigIncrements('attend_id')->first(); // Ensure this matches the original column definition
            }
        });
    }
}
