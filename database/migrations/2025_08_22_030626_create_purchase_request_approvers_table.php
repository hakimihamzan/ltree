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
        Schema::create('purchase_request_approvers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('approval_chain_id')->constrained()->onDelete('cascade');
            $table->boolean('has_approved')->nullable(); // null = not needed, false = pending, true = approved
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            // Ensure unique combination
            $table->unique(['purchase_request_id', 'approval_chain_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_request_approvers');
    }
};
