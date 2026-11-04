<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('celebrity_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('celebrity_id')->constrained('celebrities')->cascadeOnDelete();
            $table->string('service_type', 100);
            $table->text('description')->nullable();
            $table->char('currency', 3)->default('INR');
            $table->decimal('standard_fee', 15, 2);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('celebrity_rates');
    }
};

