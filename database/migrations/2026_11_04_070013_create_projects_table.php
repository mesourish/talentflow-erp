<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_code', 50)->unique();
            $table->foreignId('quotation_id')->nullable()->unique()->constrained('quotations')->nullOnDelete();
            $table->foreignId('client_id')->constrained('clients')->restrictOnDelete();
            $table->string('project_name', 200);
            $table->string('project_type', 100);
            $table->foreignId('project_manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->longText('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('budget_amount', 15, 2)->default(0);
            $table->char('currency', 3)->default('INR');
            $table->string('status', 30)->default('planned');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};

