<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_has_company_has_succursales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('succursale_id')
                ->constrained('succursales')->onDelete('cascade');
            $table->foreignId('hasuser_id')->constrained('user_has_companies')
                ->onDelete('cascade');
            $table->boolean('status')->default(true);
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('user_has_company_has_succursales');
    }
};
