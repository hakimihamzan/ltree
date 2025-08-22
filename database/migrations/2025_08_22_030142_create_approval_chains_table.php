<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('approval_chains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Use User model
            $table->timestamps();
        });

        DB::statement('CREATE EXTENSION IF NOT EXISTS ltree;');

        // Add ltree column and index
        DB::statement('ALTER TABLE approval_chains ADD COLUMN path ltree');
        DB::statement('CREATE INDEX approval_chains_path_gist ON approval_chains USING GIST (path)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_chains');
    }
};
