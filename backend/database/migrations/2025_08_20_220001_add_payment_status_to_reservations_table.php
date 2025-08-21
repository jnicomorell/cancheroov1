<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('reservations', 'payment_status')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('reservations', 'payment_status')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->dropColumn('payment_status');
            });
        }
    }
};
