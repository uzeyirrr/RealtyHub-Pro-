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
        Schema::table('properties', function (Blueprint $table) {
            // Mevcut sütunları kontrol et ve güncelle
            if (!Schema::hasColumn('properties', 'agent_id')) {
                $table->foreignId('agent_id')->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('properties', 'title')) {
                $table->string('title');
            }
            if (!Schema::hasColumn('properties', 'type')) {
                $table->string('type');
            }
            if (!Schema::hasColumn('properties', 'status')) {
                $table->string('status');
            }
            if (!Schema::hasColumn('properties', 'price')) {
                $table->decimal('price', 12, 2);
            }
            if (!Schema::hasColumn('properties', 'description')) {
                $table->text('description');
            }
            if (!Schema::hasColumn('properties', 'location')) {
                $table->json('location');
            }
            if (!Schema::hasColumn('properties', 'details')) {
                $table->json('details');
            }
            if (!Schema::hasColumn('properties', 'media')) {
                $table->json('media');
            }
            if (!Schema::hasColumn('properties', 'stats')) {
                $table->json('stats')->nullable();
            }
            if (!Schema::hasColumn('properties', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // Eklenen sütunları kaldır
            $table->dropForeign(['agent_id']);
            $table->dropColumn([
                'agent_id',
                'title',
                'type',
                'status',
                'price',
                'description',
                'location',
                'details',
                'media',
                'stats',
                'deleted_at'
            ]);
        });
    }
};
