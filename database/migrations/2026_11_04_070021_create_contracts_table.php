<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_no', 50)->unique();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('celebrity_id')->nullable()->constrained('celebrities')->nullOnDelete();
            $table->string('contract_type', 100);
            $table->string('contract_title', 200);
            $table->date('start_date');
            $table->date('expiry_date')->nullable();
            $table->text('usage_rights')->nullable();
            $table->string('territory', 150)->nullable();
            $table->json('permitted_channels')->nullable();
            $table->boolean('exclusivity')->default(false);
            $table->dateTime('signed_at')->nullable();
            $table->string('status', 30)->default('draft');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index(['expiry_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};

