<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique(); // Add username
            $table->string('phone_no')->nullable(); // Add phone number
            $table->enum('role', ['admin', 'supervisor', 'user', 'officer'])->default('user'); // Add role with a default value
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'phone_no', 'role']); // Drop the columns if rolling back
        });
    }
}
