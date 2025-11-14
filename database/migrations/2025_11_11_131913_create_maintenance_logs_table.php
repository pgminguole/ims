<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->date('maintenance_date');
            $table->string('type',191); // preventive, corrective, upgrade
            $table->text('description');
            $table->text('actions_taken');
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('technician',191)->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->foreignId('performed_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('maintenance_logs');
    }
};