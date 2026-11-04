<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('client_code', 30)->unique();
            $table->string('company_name', 200);
            $table->string('client_type', 50)->default('brand');
            $table->string('industry', 100)->nullable();
            $table->string('website', 255)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->text('billing_address')->nullable();
            $table->string('tax_number', 100)->nullable();
            $table->unsignedInteger('payment_terms_days')->default(30);
            $table->string('status', 30)->default('active');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['company_name', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};

