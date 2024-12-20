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
        Schema::table('user_has_companies', function (Blueprint $table) {
            $table->foreignUuid('company_id')->constrained('companies')
                ->after('fonction_id');
            $table->foreignUuid('user_id')->constrained('users')
                ->after('company_id')
                ->onDelete('cascade');
            $table->unique(['company_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_has_companies', function (Blueprint $table) {
            $table->dropColumn('company_id');
            $table->dropColumn('user_id');
        });
    }
};
