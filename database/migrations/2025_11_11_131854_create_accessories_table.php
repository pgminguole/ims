<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accessories', function (Blueprint $table) {
            $table->id();
            $table->string('name',191);
            $table->text('description')->nullable();
            $table->string('serial_number',191)->nullable();
            $table->string('model',191)->nullable();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->enum('condition', ['excellent', 'good', 'fair', 'poor', 'broken'])->default('good');
            $table->date('date_acquired')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('accessories');
    }
};