<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('celebrity_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->restrictOnDelete();
            $table->foreignId('celebrity_id')->constrained('celebrities')->restrictOnDelete();
            $table->decimal('gross_amount', 15, 2);
            $table->decimal('commission_amount', 15, 2);
            $table->decimal('other_deductions', 15, 2)->default(0);
            $table->decimal('net_payable', 15, 2);
            $table->string('status', 30)->default('draft');
            $table->dateTime('paid_at')->nullable();
            $table->string('payment_reference', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('celebrity_payouts');
    }
};

