<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropStatusColumnFromComplaintsTable extends Migration
{
    public function up()
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn('status'); // Drop the 'status' column
        });
    }

    public function down()
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->string('status', 255)->default('pending'); // Add the 'status' column back in case of rollback
        });
    }
}
