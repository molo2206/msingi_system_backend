<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_has_abonnements', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('module_id')->constrained('modules')->onDelete('cascade');
            $table->foreignUuid('abonnement_id')->constrained('abonnements')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_has_abonnements');
    }
};
