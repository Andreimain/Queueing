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
            $table->string('contact_number'); // Contact Number
            $table->string('id_number'); // ID No. (Student/Visitor)
            $table->unsignedBigInteger('office_id'); // Reference to offices table
            $table->integer('queue_number');
            $table->enum('status', ['waiting', 'serving', 'done', 'skipped'])->default('waiting');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('office_id')
                  ->references('id')
                  ->on('offices')
                  ->onDelete('cascade');
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
