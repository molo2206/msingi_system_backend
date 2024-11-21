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

        Schema::table('fonctions', function (Blueprint $table)
        {
              $table->foreignUuid('departement_id')
              ->constrained('departements')
              ->onDelete('cascade')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fonctions', function (Blueprint $table) {
            $table->dropColumn('departement_id');
        });
    }
};
