<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('asset_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->string('action',191); // assigned, transferred, maintenance, etc.
            $table->text('description');
            $table->text('old_values')->nullable();
            $table->text('new_values')->nullable();
            $table->foreignId('performed_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('performed_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('asset_histories');
    }
};