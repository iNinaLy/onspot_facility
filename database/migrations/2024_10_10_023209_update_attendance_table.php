<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->date('attend_date')->nullable(); // Attendance date
            $table->time('attend_in')->nullable(); // Time of attendance check-in
            $table->string('attend_status')->nullable(); // Attendance status (present, absent, etc.)
            $table->unsignedBigInteger('cleaner_id'); // Foreign key for cleaner

            // If not already defined, add foreign key constraint
            $table->foreign('cleaner_id')->references('id')->on('cleaners')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropColumn(['attend_date', 'attend_in', 'attend_status', 'cleaner_id']);
        });
    }
}
