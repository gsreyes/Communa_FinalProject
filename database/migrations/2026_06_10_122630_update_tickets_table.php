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
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('ticket_category_id')->nullable()->after('type')->constrained('ticket_categories')->onDelete('restrict');
            $table->foreignId('unit_id')->nullable()->after('ticket_category_id')->constrained('units')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->after('admin_response')->constrained('users', 'id')->onDelete('set null');
            $table->timestamp('resolved_at')->nullable()->after('assigned_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['ticket_category_id']);
            $table->dropForeignKeyIfExists(['unit_id']);
            $table->dropForeignKeyIfExists(['assigned_to']);
            $table->dropColumn([
                'ticket_category_id',
                'unit_id',
                'assigned_to',
                'resolved_at'
            ]);
        });
    }
};
