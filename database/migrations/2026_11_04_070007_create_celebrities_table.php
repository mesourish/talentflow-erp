<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('celebrities', function (Blueprint $table) {
            $table->id();
            $table->string('talent_code', 30)->unique();
            $table->foreignId('category_id')->constrained('talent_categories')->restrictOnDelete();
            $table->string('stage_name', 150);
            $table->string('legal_name', 150)->nullable();
            $table->longText('biography')->nullable();
            $table->string('base_city', 150)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('manager_name', 150)->nullable();
            $table->string('manager_contact', 50)->nullable();
            $table->string('profile_image', 255)->nullable();
            $table->string('availability_status', 30)->default('available');
            $table->string('profile_status', 30)->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['category_id', 'profile_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('celebrities');
    }
};

