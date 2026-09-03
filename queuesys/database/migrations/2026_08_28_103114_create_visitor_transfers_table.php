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
        Schema::create('visitor_transfers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('visitor_id')
                ->constrained('visitors')
                ->cascadeOnDelete();

            $table->foreignId('from_office_id')
                ->constrained('offices')
                ->restrictOnDelete();

            $table->foreignId('to_office_id')
                ->constrained('offices')
                ->restrictOnDelete();

            $table->unsignedInteger('from_queue_number');
            $table->unsignedInteger('to_queue_number');

            $table->foreignId('transferred_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('transferred_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_transfers');
    }
};
