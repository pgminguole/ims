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
        Schema::table('assets', function (Blueprint $table) {
            $table->enum('record_type', ['assignment', 'inventory'])->default('inventory')->after('status');
            $table->boolean('is_audited')->default(false)->after('audit_notes');
            $table->timestamp('audited_at')->nullable()->after('is_audited');
            $table->foreignId('audited_by_id')->nullable()->constrained('users')->onDelete('set null')->after('audited_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['audited_by_id']);
            $table->dropColumn(['record_type', 'is_audited', 'audited_at', 'audited_by_id']);
        });
    }
};
