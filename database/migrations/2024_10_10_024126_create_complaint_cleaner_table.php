<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplaintCleanerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complaint_cleaner', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->unsignedBigInteger('comp_id'); // Foreign key referencing complaints table
            $table->unsignedBigInteger('cleaner_id'); // Foreign key referencing cleaners table
            $table->integer('no_of_cleaners')->default(1); // Number of cleaners assigned to a complaint
            $table->unsignedBigInteger('assigned_by')->nullable(); // Who assigned the cleaner
            $table->timestamp('assigned_date')->nullable(); // Date of assignment
            $table->timestamps(); // Created at and updated at timestamps

            // Foreign key constraints
            $table->foreign('comp_id')->references('id')->on('complaints')->onDelete('cascade');
            $table->foreign('cleaner_id')->references('id')->on('cleaners')->onDelete('cascade');
            $table->foreign('assigned_by')->references('id')->on('officers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('complaint_cleaner');
    }
}
