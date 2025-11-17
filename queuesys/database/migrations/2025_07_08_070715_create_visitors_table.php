<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('contact_number');
            $table->string('id_number')->nullable();
            $table->unsignedBigInteger('office_id');
            $table->integer('queue_number');
            $table->integer('ticket_number');
            $table->enum('type', ['student', 'visitor'])->nullable();
            $table->unsignedBigInteger('cashier_id')->nullable();

            $table->foreign('office_id')
            ->references('id')
            ->on('offices')
            ->onDelete('cascade');

            $table->foreign('cashier_id')
            ->references('id')
            ->on('users')
            ->onDelete('set null');

            $table->boolean('priority')->default(false);
            $table->enum('status', ['waiting', 'serving', 'done', 'skipped'])->default('waiting');
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
