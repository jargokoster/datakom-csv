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
        Schema::create('processing_rules', function (Blueprint $table) {
            $table->id();
	    $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
	    $table->string('type'); // multiply, remove, regex, date etc
	    $table->string('column_name');
	    $table->json('config')->nullable();
	    $table->unsignedInteger('order')->default(0);
	    $table->boolean('active')->default(true);
            $table->timestamps();

	    $table->unique(['supplier_id', 'column_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processing_rules');
    }
};
