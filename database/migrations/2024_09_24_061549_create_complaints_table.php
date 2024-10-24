<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplaintsTable extends Migration
{
    public function up()
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id(); // Primary key (auto-increment)
            
            $table->date('comp_date'); // Date of the complaint
            $table->time('comp_time'); // Time of the complaint
            $table->text('comp_desc'); // Description of the complaint
            $table->string('comp_location', 255); // Location of the complaint
            $table->string('comp_status', 20)->default('pending'); // Complaint status

            // Store binary image data as LONGBLOB
            $table->longBlob('comp_image')->nullable(); 

            $table->unsignedBigInteger('officer_id'); // Foreign key for officer
            
            // Supervisor who assigned the complaint to a cleaner
            $table->string('assigned_by')->nullable(); // Store the supervisor's name who assigned the task
            $table->timestamp('assigned_date')->nullable(); // The date the task was assigned

            // New columns
            $table->unsignedInteger('no_of_cleaners')->nullable(); // Number of cleaners assigned
            $table->unsignedBigInteger('cleaner_id')->nullable(); // ID of the main cleaner

            // Foreign key constraints
            $table->foreign('officer_id')->references('id')->on('officers')->onDelete('cascade');
            $table->foreign('cleaner_id')->references('id')->on('cleaners')->onDelete('set null');
            
            $table->timestamps(); // created_at and updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('complaints');
    }
}
