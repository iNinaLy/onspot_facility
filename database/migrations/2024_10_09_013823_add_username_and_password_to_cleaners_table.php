<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('cleaners', function (Blueprint $table) {
            // Comment out these lines if they exist
            // $table->string('username')->after('cleaner_phoneNo');
            // $table->string('password')->after('username');
        });
    }
    
    
    public function down()
    {
        Schema::table('cleaners', function (Blueprint $table) {
            $table->dropColumn(['username', 'password']);
        });
    }
    
};
