<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameAttendancePrimaryKey extends Migration
{
    public function up()
    {
        Schema::table('attendance', function (Blueprint $table) {
            // Drop the primary key constraint for attend_id
            $table->dropPrimary(['attend_id']); 

            // Rename the attend_id column to id
            $table->renameColumn('attend_id', 'id');

            // Set the new id column as the primary key
            $table->primary('id');
        });
    }

    public function down()
    {
        Schema::table('attendance', function (Blueprint $table) {
            // Drop the primary key constraint for id
            $table->dropPrimary(['id']);

            // Rename the id column back to attend_id
            $table->renameColumn('id', 'attend_id');

            // Re-add the primary key constraint for attend_id
            $table->primary('attend_id');
        });
    }
}
