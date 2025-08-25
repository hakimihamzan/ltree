<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing paths to remove department prefix
        // Convert ltree to text, process, then convert back to ltree
        DB::statement("
            UPDATE approval_chains
            SET path = CASE
                WHEN position('.' in path::text) > 0 THEN
                    substring(path::text from position('.' in path::text) + 1)::ltree
                ELSE
                    path
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore department prefix to paths
        DB::statement("
            UPDATE approval_chains
            SET path = (department_id::text || '.' || path::text)::ltree
        ");
    }
};
