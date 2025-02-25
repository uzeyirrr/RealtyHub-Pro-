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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('office_id')->nullable()->constrained('real_estate_offices')->nullOnDelete();
            $table->string('phone')->nullable();
            $table->string('alternate_phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('customer_type')->default('individual'); // individual, corporate
            $table->string('tax_number')->nullable();
            $table->string('tax_office')->nullable();
            $table->string('company_name')->nullable();
            $table->json('preferences')->nullable(); // Arama tercihleri
            $table->json('search_history')->nullable(); // Arama geçmişi
            $table->json('viewed_properties')->nullable(); // Görüntülenen ilanlar
            $table->json('favorite_properties')->nullable(); // Favori ilanlar
            $table->string('lead_source')->nullable(); // Müşteri kaynağı
            $table->string('lead_status')->default('new'); // new, contacted, qualified, lost
            $table->text('notes')->nullable();
            $table->timestamp('last_contact')->nullable();
            $table->timestamp('next_followup')->nullable();
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
        Schema::dropIfExists('customers');
    }
};
