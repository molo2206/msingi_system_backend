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
        Schema::table('modules', function (Blueprint $table) {
            $table->float('monthly_price')->after('fonctionnalite');
            $table->float('yearly_price')->after('monthly_price');
            $table->float('lifetime_price')->after('yearly_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn('monthly_price');
            $table->dropColumn('yearly_price');
            $table->dropColumn('lifetime_price');
        });
    }
};
