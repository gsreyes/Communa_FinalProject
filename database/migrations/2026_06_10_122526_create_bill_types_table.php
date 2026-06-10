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
         Schema::create('bill_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Water, Association Dues, Maintenance Fee, Rules Violation, Assessment
            $table->text('description')->nullable();
            $table->string('code')->unique(); // WATER, DUES, MAINTENANCE, VIOLATION, ASSESSMENT
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_types');
    }
};
