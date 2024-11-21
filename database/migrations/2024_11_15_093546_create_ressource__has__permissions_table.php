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
        Schema::create('ressource__has__permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_has_company_id')
            ->constrained('user_has_companies')->onDelete('cascade');
            $table->foreignUuid('ressource_id')->constrained('ressources')->onDelete('cascade');
            $table->boolean('create')->default(false);
            $table->boolean('update')->default(false);
            $table->boolean('delete')->default(false);
            $table->boolean('read')->default(false);
            $table->boolean('status')->default(false);
            $table->timestamps(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ressource__has__permissions');
    }
};
