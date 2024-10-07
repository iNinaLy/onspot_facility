<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyCompStatusColumnInComplaintsTable extends Migration
{
    public function up()
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->enum('comp_status', ['Pending', 'Notified', 'Ongoing', 'Completed'])
                  ->default('Pending')
                  ->change(); // Modify the column to an enum
        });
    }

    public function down()
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->string('comp_status', 255)->default('pending')->change(); // Revert the change
        });
    }
}
