<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('succursales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained('companies')
                ->onDelete('cascade');
            $table->string('name');
            $table->string('adresse');
            $table->string('email');
            $table->string('phone');
            $table->string('lat')->nullable();
            $table->string('long')->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('succursales');
    }
};
