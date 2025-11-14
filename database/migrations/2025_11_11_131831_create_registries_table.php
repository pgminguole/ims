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
        Schema::create('registries', function (Blueprint $table) {
            $table->id();
            $table->string('name',191);
            $table->string('code',191)->nullable();
            $table->string('email',191)->nullable();
            $table->string('status',191)->default('Published');
            $table->foreignId('location_id');
            $table->foreignId('region_id')->nullable();
            $table->string('slug',191)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registries');
    }
};
