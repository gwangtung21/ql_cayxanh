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
        Schema::create('trees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('scientific_name')->nullable();
            $table->foreignId('category_id')->constrained('tree_categories');
            $table->foreignId('location_id')->constrained('locations');
            $table->date('planting_date')->nullable();
            $table->decimal('height', 5, 2)->nullable();
            $table->decimal('diameter', 5, 2)->nullable();
            $table->enum('health_status',['excellent', 'good', 'fair', 'poor'])->default('good');
            $table->string('image_url')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trees');
    }
};
