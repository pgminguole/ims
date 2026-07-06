<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function backup()
    {
        try {
            Artisan::call('backup:run', ['--only-db' => true]);
            
            return response()->json([
                'success' => true,
                'message' => 'Database backup completed successfully!',
                'download_url' => route('settings.backup.download')
            ]);
        } catch (\Exception $e) {
            Log::error('Backup failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Backup failed. Please check the logs.'
            ], 500);
        }
    }

    public function downloadLatestBackup()
    {
        $disk = \Illuminate\Support\Facades\Storage::disk(config('backup.backup.destination.disks')[0] ?? 'local');
        $files = $disk->files(config('backup.backup.name'));
        
        $latestFile = collect($files)->filter(function ($file) {
            return str_ends_with($file, '.zip');
        })->sortByDesc(function ($file) use ($disk) {
            return $disk->lastModified($file);
        })->first();

        if ($latestFile) {
            return response()->download($disk->path($latestFile));
        }

        return back()->with('error', 'No backup file found.');
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sql'
        ]);

        try {
            $path = $request->file('backup_file')->getRealPath();
            $sql = file_get_contents($path);
            DB::unprepared($sql);

            return back()->with('success', 'Database restored successfully!');
        } catch (\Exception $e) {
            Log::error('Restore failed: ' . $e->getMessage());
            return back()->with('error', 'Restore failed. Ensure the SQL file is valid.');
        }
    }

    public function wipe(Request $request)
    {
        $request->validate([
            'confirmation' => 'required|string|in:confirm wipe',
            'wipe_options' => 'required|array',
            'wipe_options.*' => 'string|in:assets,users,locations,categories,regions'
        ]);

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            $options = collect($request->wipe_options);

            if ($options->contains('assets')) {
                DB::table('assets')->truncate();
                DB::table('asset_histories')->truncate();
                DB::table('maintenance_logs')->truncate();
                DB::table('obsolete_assets')->truncate();
                DB::table('attachments')->truncate();
                DB::table('accessories')->truncate();
                DB::table('dts')->truncate();
            }

            if ($options->contains('users')) {
                $adminRoleIds = \App\Models\Role::whereIn('name', [
                    'super_admin', 'admin', 'auditor', 'ict_system_admin', 'rao'
                ])->pluck('id');
                
                if ($adminRoleIds->isNotEmpty()) {
                    \App\Models\User::whereNotIn('role_id', $adminRoleIds)->delete();
                }
            }
            
            if ($options->contains('locations')) {
                DB::table('offices')->truncate();
                DB::table('courts')->truncate();
                DB::table('locations')->truncate();
            }

            if ($options->contains('categories')) {
                DB::table('categories')->truncate();
            }

            if ($options->contains('regions')) {
                DB::table('regions')->truncate();
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            return redirect()->back()->with('success', 'Selected data wiped successfully.');
        } catch (\Exception $e) {
            Log::error('Wipe failed: ' . $e->getMessage());
            return back()->with('error', 'Data wipe failed. Please check logs.');
        }
    }
}
