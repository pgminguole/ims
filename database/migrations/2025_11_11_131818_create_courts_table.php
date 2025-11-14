<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('courts', function (Blueprint $table) {
            $table->id();
            $table->string('name',191);
            $table->string('type',191); // Supreme Court, High Court, Circuit Court, etc.
            $table->string('code',191)->unique();
            $table->foreignId('region_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->nullable()->constrained()->onDelete('set null');
            $table->text('address')->nullable();
            $table->string('presiding_judge',191)->nullable();
            $table->string('registry_officer',191)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('courts');
    }
};