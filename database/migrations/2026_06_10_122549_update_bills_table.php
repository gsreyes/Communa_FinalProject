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
        Schema::table('bills', function (Blueprint $table) {
            $table->foreignId('unit_id')->nullable()->after('user_id')->constrained('units')->onDelete('cascade');
            $table->foreignId('bill_type_id')->nullable()->after('unit_id')->constrained('bill_types')->onDelete('restrict');
            $table->text('description')->nullable()->after('amount');
            $table->date('billing_period_start')->nullable()->after('description');
            $table->date('billing_period_end')->nullable()->after('billing_period_start');
            $table->string('reference_number')->nullable()->unique()->after('proof_of_payment');
            $table->decimal('paid_amount', 10, 2)->nullable()->after('reference_number');
            $table->timestamp('paid_at')->nullable()->after('paid_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['unit_id']);
            $table->dropForeignKeyIfExists(['bill_type_id']);
            $table->dropColumn([
                'unit_id',
                'bill_type_id',
                'description',
                'billing_period_start',
                'billing_period_end',
                'reference_number',
                'paid_amount',
                'paid_at'
            ]);
        });
    }
};
