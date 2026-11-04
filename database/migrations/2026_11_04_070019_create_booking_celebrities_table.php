<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('booking_celebrities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('celebrity_id')->constrained('celebrities')->restrictOnDelete();
            $table->string('assignment_role', 100)->nullable();
            $table->decimal('agreed_fee', 15, 2)->nullable();
            $table->dateTime('arrival_at')->nullable();
            $table->timestamps();

            $table->unique(['booking_id', 'celebrity_id']);
            $table->index('celebrity_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_celebrities');
    }
};

