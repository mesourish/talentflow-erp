<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('celebrity_social_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('celebrity_id')->constrained('celebrities')->cascadeOnDelete();
            $table->string('platform', 50);
            $table->string('profile_url', 255);
            $table->unsignedBigInteger('follower_count')->nullable();
            $table->decimal('engagement_rate', 7, 3)->nullable();
            $table->dateTime('last_verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('celebrity_social_profiles');
    }
};

