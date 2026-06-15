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
        Schema::create('csv_mappings', function (Blueprint $table) {
            $table->id();
	    $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
	    $table->string('source_column');
	    $table->string('target_column');
            $table->timestamps();

	    $table->unique(['supplier_id', 'source_column']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('csv_mappings');
    }
};
