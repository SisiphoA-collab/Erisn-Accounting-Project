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
        Schema::create('stipends', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('learner_id');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['Pending', 'Paid', 'Rejected']);
            $table->string('month', 20);
            $table->string('receipt_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stipends');
    }
};
