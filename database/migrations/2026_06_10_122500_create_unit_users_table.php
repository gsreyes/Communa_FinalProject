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
        Schema::create('unit_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('occupant_type', ['owner', 'tenant']); // owner or tenant
            $table->date('move_in_date')->nullable();
            $table->date('move_out_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Ensure one user can occupy one unit with one occupant type at a time
            $table->unique(['unit_id', 'user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_users');
    }
};
