<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name',191);
            $table->string('building',191)->nullable();
            $table->string('floor',191)->nullable();
            $table->string('room',191)->nullable();
            $table->foreignId('region_id')->constrained()->onDelete('cascade');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('locations');
    }
};