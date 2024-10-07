<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->enum('comp_status', ['Pending', 'Notified', 'Ongoing', 'Completed'])->default('Pending')->change();
        });
    }

    public function down()
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->string('comp_status', 255)->default('pending')->change();
        });
    }

};
