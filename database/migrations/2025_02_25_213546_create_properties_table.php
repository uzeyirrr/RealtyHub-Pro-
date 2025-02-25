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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained('real_estate_offices')->cascadeOnDelete();
            $table->foreignId('agent_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('type'); // Daire, Villa, Arsa vs.
            $table->string('status'); // Satılık, Kiralık
            $table->decimal('price', 12, 2);
            $table->string('currency')->default('TRY');
            $table->integer('rooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->integer('floor')->nullable();
            $table->integer('total_floors')->nullable();
            $table->integer('age')->nullable();
            $table->decimal('gross_sqm', 10, 2)->nullable();
            $table->decimal('net_sqm', 10, 2)->nullable();
            $table->string('heating_type')->nullable();
            $table->boolean('is_furnished')->default(false);
            $table->string('address');
            $table->string('city');
            $table->string('district');
            $table->string('neighborhood');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->json('features')->nullable(); // Özellikler (Havuz, Otopark vs.)
            $table->json('media')->nullable(); // Fotoğraflar, videolar
            $table->string('video_url')->nullable();
            $table->string('virtual_tour_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('featured_until')->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('favorite_count')->default(0);
            $table->string('status_text')->default('available'); // available, sold, rented
            $table->decimal('commission_rate', 5, 2)->nullable();
            $table->json('seo')->nullable(); // SEO meta verileri
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
