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
        Schema::table('companies', function(Blueprint $table){
              $table->foreignUuid('secteur_id')
              ->constrained('secteur_activities')->after('num_impot')
              ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function(Blueprint $table){
            $table->dropColumn('secteur_id');
        });
    }
};
