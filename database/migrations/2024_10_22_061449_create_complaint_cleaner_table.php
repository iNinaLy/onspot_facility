<?php

// database/migrations/xxxx_xx_xx_create_complaint_cleaner_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplaintCleanerTable extends Migration
{
    public function up()
    {
        Schema::create('complaint_cleaner', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('complaint_id');
            $table->unsignedBigInteger('cleaner_id');
            $table->integer('no_of_cleaners');
            $table->unsignedBigInteger('assigned_by');
            $table->timestamp('assigned_date');
            $table->timestamps();

            // Foreign keys
            $table->foreign('complaint_id')->references('id')->on('complaints')->onDelete('cascade');
            $table->foreign('cleaner_id')->references('id')->on('cleaners')->onDelete('cascade');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('complaint_cleaner');
    }
}
