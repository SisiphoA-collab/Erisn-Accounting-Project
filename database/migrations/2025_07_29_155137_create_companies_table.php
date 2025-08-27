<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('companies', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT
            $table->string('name');
            $table->string('industry')->nullable();
            $table->string('logo')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();

            // Force engine + charset (important for FKs)
            $table->engine = 'InnoDB';
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
