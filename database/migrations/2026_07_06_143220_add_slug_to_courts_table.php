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
        if (!Schema::hasColumn('courts', 'slug')) {
            Schema::table('courts', function (Blueprint $table) {
                $table->string('slug')->nullable()->unique()->after('name');
            });
        }

        // Generate slugs for existing courts
        $courts = \App\Models\Court::all();
        foreach ($courts as $court) {
            $baseSlug = \Illuminate\Support\Str::slug($court->name);
            $slug = $baseSlug;
            $counter = 1;
            while (\App\Models\Court::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $court->slug = $slug;
            $court->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courts', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
