<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplaintCleanerTable extends Migration
{
    public function up()
    {
        Schema::create('complaint_cleaner', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id')->constrained('complaints')->onDelete('cascade'); // Foreign key for complaints table
            $table->foreignId('cleaner_id')->constrained('cleaners')->onDelete('cascade'); // Foreign key for cleaners table
            $table->integer('no_of_cleaners');
            $table->foreignId('assigned_by')->constrained('users')->onDelete('cascade'); // Assuming users table has admin/supervisor data
            $table->timestamp('assigned_date');
            $table->timestamps();
        });
    }        

    public function down()
    {
        Schema::dropIfExists('complaint_cleaner');
    }
}

