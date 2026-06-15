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
        Schema::create('output_columns', function (Blueprint $table) {
            $table->id();
	    $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
	    $table->string('name');
	    $table->unsignedInteger('order')->default(0);
	    $table->boolean('active')->default(true);
	    $table->boolean('hidden')->default(false);
            $table->timestamps();

	    $table->unique(['supplier_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('output_columns');
    }
};
