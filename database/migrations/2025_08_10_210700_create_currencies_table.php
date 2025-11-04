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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // np. USD, PLN, EUR
            $table->string('symbol', 5);  // $ zł €
            $table->decimal('rate', 15, 6)->comment('kurs wymiany względem USD np 1zł=0.270960USD, 1EUR=1.152850USD');  // kurs wymiany względem np. USD
            $table->foreignId('language_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
