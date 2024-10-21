<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameSIdToIdInSupervisorsTable extends Migration
{
    public function up()
    {
        Schema::table('supervisors', function (Blueprint $table) {
            $table->renameColumn('s_id', 'id');
        });
    }

    public function down()
    {
        Schema::table('supervisors', function (Blueprint $table) {
            $table->renameColumn('id', 's_id');
        });
    }
}
