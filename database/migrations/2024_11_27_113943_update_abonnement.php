<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('abonnements', function (Blueprint $table) {
            $table->foreignUuid('plan_id')->constrained('plans')
            ->onDelete('cascade')->after('company_id');
            $table->float('total_price')->after('plan_id');
        });
    }
    public function down(): void
    {
        Schema::table('abonnements', function (Blueprint $table) {
            $table->dropColumn('plan_id');
            $table->dropColumn('total_price');
        });
    }
};
