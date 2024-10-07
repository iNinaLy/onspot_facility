<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupervisorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supervisors', function (Blueprint $table) {
            // Primary key
            $table->id('s_id');  // Setting primary key as s_id

            // Fields for supervisor data
            $table->string('s_email')->unique();  // Unique email field
            $table->string('s_pass');             // Password field
            $table->string('s_name');             // Name of the supervisor
            $table->string('s_phoneNo');          // Supervisor's phone number

            // Timestamps for created_at and updated_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supervisors');  // Rollback operation
    }
}