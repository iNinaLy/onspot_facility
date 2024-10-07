<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id('task_id');  // Primary key
            $table->string('task_name');
            $table->integer('no_of_cleaner');
            $table->unsignedBigInteger('comp_id');  // Foreign key for complaints
            $table->unsignedBigInteger('s_id');     // Foreign key for supervisors
            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('comp_id')->references('comp_id')->on('complaints')->onDelete('cascade');
            $table->foreign('s_id')->references('s_id')->on('supervisors')->onDelete('cascade');
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
