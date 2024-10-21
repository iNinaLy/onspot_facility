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
            $table->string('comp_status', 20)->default('pending'); // Changed from enum to string for complaint status
            
            $table->string('comp_image')->nullable(); // Store the path to the image, not the binary data
            
            $table->unsignedBigInteger('officer_id'); // Foreign key for officer
            
            // Supervisor who assigned the complaint to a cleaner
            $table->string('assigned_by')->nullable(); // Store the supervisor's name who assigned the task
            $table->timestamp('assigned_date')->nullable(); // The date the task was assigned
            
            // New columns
            $table->unsignedInteger('no_of_cleaners')->nullable(); // Number of cleaners assigned
            $table->unsignedBigInteger('cleaner_id')->nullable(); // ID of the main cleaner

            // Foreign key constraint to officers table
            $table->foreign('officer_id')->references('id')->on('officers')->onDelete('cascade');
            
            // Foreign key constraint to cleaners table
            $table->foreign('cleaner_id')->references('id')->on('cleaners')->onDelete('set null');
            
            $table->timestamps(); // created_at and updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('complaints');
    }
}

