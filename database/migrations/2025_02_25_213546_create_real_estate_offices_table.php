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
        Schema::create('real_estate_offices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->string('phone');
            $table->string('email')->unique();
            $table->string('tax_number')->unique();
            $table->string('address');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('city');
            $table->string('district');
            $table->string('website')->nullable();
            $table->boolean('is_franchise')->default(false);
            $table->foreignId('parent_office_id')->nullable()->constrained('real_estate_offices')->nullOnDelete();
            $table->json('working_hours')->nullable();
            $table->json('social_media')->nullable();
            $table->text('about')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_estate_offices');
    }
};
