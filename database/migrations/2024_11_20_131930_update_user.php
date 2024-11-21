<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('longitude')->after('google2fa_secret')->nullable();
            $table->string('latitude')->after('longitude')->nullable();
        });
    }
    public function down(): void
    {
        Schema::table('users', function(Blueprint $table){
            $table->dropColumn('users');
        });
    }
};
