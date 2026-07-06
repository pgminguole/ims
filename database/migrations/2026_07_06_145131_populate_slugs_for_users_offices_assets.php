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
        // Add slug columns if they don't exist
        $tables = ['users', 'offices', 'assets'];
        foreach ($tables as $table) {
            if (!Schema::hasColumn($table, 'slug')) {
                Schema::table($table, function (Blueprint $tableBlueprint) {
                    $tableBlueprint->string('slug')->nullable()->unique();
                });
            }
        }

        // Generate slugs for Users
        $users = \App\Models\User::all();
        foreach ($users as $user) {
            if (empty($user->slug)) {
                $baseSlug = \Illuminate\Support\Str::slug($user->first_name . '-' . $user->last_name);
                if (empty($baseSlug)) { $baseSlug = 'user-' . $user->id; }
                $slug = $baseSlug;
                $counter = 1;
                while (\App\Models\User::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }
                $user->slug = $slug;
                $user->save();
            }
        }

        // Generate slugs for Offices
        $offices = \App\Models\Office::all();
        foreach ($offices as $office) {
            if (empty($office->slug)) {
                $baseSlug = \Illuminate\Support\Str::slug($office->name);
                if (empty($baseSlug)) { $baseSlug = 'office-' . $office->id; }
                $slug = $baseSlug;
                $counter = 1;
                while (\App\Models\Office::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }
                $office->slug = $slug;
                $office->save();
            }
        }

        // Generate slugs for Assets
        $assets = \App\Models\Asset::all();
        foreach ($assets as $asset) {
            if (empty($asset->slug)) {
                $baseSlug = \Illuminate\Support\Str::slug($asset->tag_number);
                if (empty($baseSlug)) { $baseSlug = 'asset-' . $asset->id; }
                $slug = $baseSlug;
                $counter = 1;
                while (\App\Models\Asset::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }
                $asset->slug = $slug;
                $asset->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
