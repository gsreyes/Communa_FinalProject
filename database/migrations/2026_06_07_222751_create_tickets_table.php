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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id(); // ticket_id
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['Concern', 'Request']);
            $table->text('description');
            $table->string('attachment')->nullable();
            $table->enum('status', ['Pending', 'Resolved', 'Rejected'])->default('Pending');
            $table->text('admin_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
