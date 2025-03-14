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
        Schema::table('recurring_expenses', function (Blueprint $table) {
            $table->dateTime('start_date')->change();
            $table->dateTime('last_processed_at')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recurring_expenses', function (Blueprint $table) {
            $table->date('start_date');
            $table->date('last_processed_at')->nullable();
        });
    }
};
