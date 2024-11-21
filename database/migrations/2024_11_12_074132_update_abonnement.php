<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('abonnements', function (Blueprint $table)
        {
              $table->foreignUuid('type_abon_id')
              ->constrained('type_abonnements')
              ->onDelete('cascade')->after('expires');
        });
    }

    public function down(): void
    {
        Schema::table('abonnements', function (Blueprint $table) {
            $table->dropColumn('type_abon_id');
        });
    }
};
