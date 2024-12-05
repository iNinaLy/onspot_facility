<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCleanerIdInComplaintCleaner extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('complaint_cleaner', function (Blueprint $table) {
            // Add the new foreign key constraint
            $table->foreign('cleaner_id')
                ->references('user_id')->on('cleaners')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('complaint_cleaner', function (Blueprint $table) {
            // Drop the updated foreign key constraint
            $table->dropForeign(['cleaner_id']);
        });
    }
}
