<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->string('code', 6)->nullable(); // Store a 6-digit code
            $table->dropColumn('token'); // Remove the token column
        });
    }
    
    public function down()
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->dropColumn('code');
            $table->string('token')->nullable(); // Re-add the token column
        });
    }
    
};
