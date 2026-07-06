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
        Schema::create('obsolete_assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_name');
            $table->string('serial_number')->nullable();
            $table->string('category')->nullable(); // Using string as per plan
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->date('date_acquired')->nullable();
            $table->date('date_obsolete');
            $table->text('reason')->nullable();
            $table->string('disposal_method')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // The one who recorded it
            $table->string('reported_by_name')->nullable();
            
            // New fields for location tracking
            $table->foreignId('region_id')->nullable()->constrained('regions')->nullOnDelete();
            $table->foreignId('court_id')->nullable()->constrained('courts')->nullOnDelete();
            $table->foreignId('office_id')->nullable()->constrained('offices')->nullOnDelete();
            $table->foreignId('owner_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('target_type')->nullable(); // court, office, user
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obsolete_assets');
    }
};
