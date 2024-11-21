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
         Schema::table('user_has_companies', function (Blueprint $table){
             $table->foreignUuid('fonction_id')->constrained('fonctions')
             ->after('user_id')
             ->onDelete('cascade');
         });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('user_has_companies', function (Blueprint $table){
             $table->dropColumn('fonction_id');
         });
    }
};
