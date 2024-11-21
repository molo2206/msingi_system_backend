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
        Schema::create('ressources', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable();
            $table->string('label')->nullable();
            $table->string('fonctionnalite')->nullable();
            $table->foreignUuid('module_id')->constrained('modules')
                ->onDelete('cascade');
            $table->boolean('status')->default(true);
            $table->boolean('deleted')->default(false);
            $table->timestamps(true);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ressources');
    }
};
