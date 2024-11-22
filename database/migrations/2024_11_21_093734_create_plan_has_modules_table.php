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
        Schema::create('plan_has_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('plan_id')->constrained('plans')->onDelete('cascade');
            $table->foreignUuid('module_id')->constrained('modules')->onDelete('cascade');
            $table->boolean('status')->default(true);
            $table->timestamps(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_has_modules');
    }
};
