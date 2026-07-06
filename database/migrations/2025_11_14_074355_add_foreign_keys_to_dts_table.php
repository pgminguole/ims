<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToDtsTable extends Migration
{
    public function up()
    {
        Schema::table('dts', function (Blueprint $table) {
            // Add foreign key constraints after assets table exists
            $table->foreign('monitor_asset_id')->references('id')->on('assets')->onDelete('set null');
            $table->foreign('splitter_asset_id')->references('id')->on('assets')->onDelete('set null');
            $table->foreign('hdmi_short_cable_asset_id')->references('id')->on('assets')->onDelete('set null');
            $table->foreign('hdmi_long_cable_asset_id')->references('id')->on('assets')->onDelete('set null');
            $table->foreign('extension_board_asset_id')->references('id')->on('assets')->onDelete('set null');
            $table->foreign('trucking_asset_id')->references('id')->on('assets')->onDelete('set null');
            $table->foreign('sony_recorder_asset_id')->references('id')->on('assets')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('dts', function (Blueprint $table) {
            $table->dropForeign(['monitor_asset_id']);
            $table->dropForeign(['splitter_asset_id']);
            $table->dropForeign(['hdmi_short_cable_asset_id']);
            $table->dropForeign(['hdmi_long_cable_asset_id']);
            $table->dropForeign(['extension_board_asset_id']);
            $table->dropForeign(['trucking_asset_id']);
            $table->dropForeign(['sony_recorder_asset_id']);
        });
    }
}