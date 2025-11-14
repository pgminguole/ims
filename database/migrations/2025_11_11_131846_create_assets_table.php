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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('slug',191);
            $table->string('asset_id',191)->unique();
            $table->string('asset_name',191);
            $table->foreignId('category_id')->nullable()->constrained();
            $table->enum('status', ['available', 'assigned', 'maintenance', 'retired', 'lost', 'disposed'])->default('available');
            $table->longText('description')->nullable();
            $table->date('purchase_date')->nullable();

          
            $table->date('recieved_date')->nullable();
            $table->date('assigned_date')->nullable();
            $table->date('returned_date')->nullable();
            $table->string('returned_reason',191)->nullable();
   
            $table->string('returnee',191)->nullable();
            $table->string('returned_to',191)->nullable();

    



            $table->decimal('purchase_cost', 15, 2)->nullable()->default(0.00);
            $table->decimal('current_value', 15, 2)->nullable()->default(0.00);
            $table->string('depreciation_method',191)->nullable();
            $table->foreignId('court_id')->nullable();
            $table->foreignId('location_id')->nullable();
            $table->foreignId('region_id')->nullable();

             $table->text('attachments')->nullable();
             
            $table->mediumText('maintenance_schedule')->nullable();

            // Specific fields for ICT Equipment
            $table->string('manufacturer',191)->nullable();
            $table->string('model',191)->nullable();
            $table->string('serial_number',191)->nullable();
            $table->mediumText('warranty_information')->nullable();

            $table->string('asset_tag',191)->unique()->nullable();
            $table->string('brand',191)->nullable();
            $table->string('supplier',191)->nullable();
            $table->string('warranty_period',191)->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->date('last_maintenance')->nullable();
            $table->date('next_maintenance')->nullable();
            $table->text('maintenance_notes')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->string('assigned_type',191)->nullable(); // judge, staff, department, court
            $table->foreignId('subcategory_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->foreignId('registry_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('ip_address',191)->nullable();
            $table->string('mac_address',191)->nullable();
            $table->text('specifications')->nullable();
            $table->enum('condition', ['excellent', 'good', 'fair', 'poor', 'broken','maintenance'])->default('good');
            
            // Add audit fields
            $table->timestamp('last_audited_at')->nullable();
            $table->foreignId('last_audited_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('audit_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['slug', 'asset_id', 'asset_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};