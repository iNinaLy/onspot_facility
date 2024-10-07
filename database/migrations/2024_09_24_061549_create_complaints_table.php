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
            $table->unsignedBigInteger('comp_id')->unique(); // Define as unsignedBigInteger and ensure it's unique
            $table->date('comp_date'); // Date of the complaint
            $table->time('comp_time'); // Time of the complaint
            $table->text('comp_desc'); // Description of the complaint
            $table->string('comp_location', 255); // Location of the complaint
            $table->enum('comp_status', ['Pending', 'Notified', 'Ongoing', 'Completed'])->default('Pending'); // Status of the complaint
            $table->binary('comp_image')->nullable(); // Image storage (optional)
            $table->unsignedBigInteger('officer_id'); // Foreign key for officer
            $table->timestamps(); // created_at and updated_at
            $table->string('assigned_cleaner_ids')->nullable(); // Can store cleaner IDs as comma-separated values or JSON
            $table->string('assigned_by')->nullable();
            $table->timestamp('assigned_date')->nullable();
            // Foreign key constraint to officers table
            $table->foreign('officer_id')->references('id')->on('officers')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('complaints');
    }
}