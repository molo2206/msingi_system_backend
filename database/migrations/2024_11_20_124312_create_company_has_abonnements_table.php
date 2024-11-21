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
        Schema::create('company_has_abonnements', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('company_id')->constrained('companies')
            ->onDelete('cascade');
            $table->foreignUuid('abonnement_id')->constrained('abonnements')->onDelete('cascade');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('company_has_abonnements');
    }
};
