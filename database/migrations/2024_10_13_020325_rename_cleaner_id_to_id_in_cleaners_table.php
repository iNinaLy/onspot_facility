<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameCleanerIdToIdInCleanersTable extends Migration
{
    public function up()
    {
        Schema::table('cleaners', function (Blueprint $table) {
            // Check if 'cleaner_id' exists before renaming
            if (Schema::hasColumn('cleaners', 'cleaner_id')) {
                $table->renameColumn('cleaner_id', 'id');
            }
        });
    }

    public function down()
    {
        Schema::table('cleaners', function (Blueprint $table) {
            // Check if 'id' exists before renaming back
            if (Schema::hasColumn('cleaners', 'id')) {
                $table->renameColumn('id', 'cleaner_id');
            }
        });
    }
}
