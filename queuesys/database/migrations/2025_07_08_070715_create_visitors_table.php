<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_number');
            $table->string('id_number')->nullable();
            $table->unsignedBigInteger('office_id')->nullable();
            $table->string('other_office')->nullable();
            $table->unsignedBigInteger('previous_office_id')->nullable();
            $table->integer('queue_number')->nullable();
            $table->string('ticket_number')->nullable();
            $table->enum('type', ['student', 'visitor'])->default('visitor');

            $table->foreignId('course_id')
                ->nullable()
                ->constrained('courses')
                ->nullOnDelete();

            $table->unsignedBigInteger('cashier_id')->nullable();

            $table->foreign('office_id')
                ->references('id')
                ->on('offices')
                ->onDelete('cascade');

            $table->foreign('previous_office_id')
                ->references('id')
                ->on('offices')
                ->onDelete('set null');

            $table->foreign('cashier_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->boolean('priority')->default(false);
            $table->enum('status', ['waiting','serving','done','skipped','transferred'])->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
