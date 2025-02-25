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
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('office_id')->constrained('real_estate_offices')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('phone');
            $table->string('photo')->nullable();
            $table->string('license_number')->unique()->nullable();
            $table->date('license_date')->nullable();
            $table->json('specializations')->nullable();
            $table->json('languages')->nullable();
            $table->text('about')->nullable();
            $table->json('social_media')->nullable();
            $table->boolean('is_manager')->default(false);
            $table->boolean('is_active')->default(true);
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->json('working_areas')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
