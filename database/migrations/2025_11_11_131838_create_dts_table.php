<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDTSTable extends Migration
{
    public function up()
    {
        Schema::create('dts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('court_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->integer('monitors_count')->default(0);
            $table->integer('splitters_count')->default(0);
            $table->integer('hdmi_short_cables_count')->default(0);
            $table->integer('hdmi_long_cables_count')->default(0);
            $table->integer('extension_boards_count')->default(0);
            $table->integer('trucking_count')->default(0);
            $table->integer('sony_recorders_count')->default(0);
            
            // Remove foreign key constraints for now, use unsignedBigInteger
            $table->unsignedBigInteger('monitor_asset_id')->nullable();
            $table->unsignedBigInteger('splitter_asset_id')->nullable();
            $table->unsignedBigInteger('hdmi_short_cable_asset_id')->nullable();
            $table->unsignedBigInteger('hdmi_long_cable_asset_id')->nullable();
            $table->unsignedBigInteger('extension_board_asset_id')->nullable();
            $table->unsignedBigInteger('trucking_asset_id')->nullable();
            $table->unsignedBigInteger('sony_recorder_asset_id')->nullable();
              $table->date('assigned_date')->nullable();
            $table->boolean('is_available')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dts');
    }
}