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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained()->cascadeOnDelete();
            $table->string('reference')->nullable();     // bank reference
            $table->string('description')->nullable();   // memo/notes
            $table->decimal('amount', 12, 2);            // positive numbers
            $table->enum('type', ['credit', 'debit']);   // credit=inflow, debit=outflow
            $table->date('date');
            $table->enum('status', ['pending', 'posted'])->default('posted');
            $table->timestamp('reconciled_at')->nullable();
            $table->foreignId('reconciled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('stipend_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};