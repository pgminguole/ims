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
        $tables = ['assets', 'courts', 'offices', 'regions', 'locations', 'users', 'categories'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'created_by')) {
                    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['assets', 'courts', 'offices', 'regions', 'locations', 'users', 'categories'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'created_by')) {
                    $table->dropConstrainedForeignId('created_by');
                }
            });
        }
    }
};
