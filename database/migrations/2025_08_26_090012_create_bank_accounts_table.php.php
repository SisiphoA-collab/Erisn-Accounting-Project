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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_holder'); // e.g. "Erisn Accounting" or learner name
            $table->string('account_number');
            $table->string('bank_name');
            $table->string('branch_code')->nullable();
            $table->enum('account_type', ['Cheque', 'Savings', 'Business', 'Other'])->default('Cheque');
            $table->enum('status', ['Active', 'Inactive'])->default('Active'); // Added status column
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};