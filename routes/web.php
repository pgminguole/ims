<?php


use App\Http\Controllers\AssetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourtController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('/', '/dashboard');

Route::middleware(['auth', 'auth.reset-password'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Categories (Enhanced)
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories/create', [CategoryController::class, 'store'])->name('categories.create');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::post('/categories/{category}/edit', [CategoryController::class, 'update'])->name('categories.edit');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');


    Route::get('/assets/import/form', [AssetController::class, 'importForm'])->name('assets.import.form');
    Route::post('/assets/import', [AssetController::class, 'import'])->name('assets.import.process');
    Route::get('/assets/import/template', [AssetController::class, 'downloadTemplate'])->name('assets.import.template');
    

    // Assets (Enhanced with new functionality)
        Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
Route::get('/assets/export', [AssetController::class, 'export'])->name('assets.export');
Route::get('/assets/export/all', [AssetController::class, 'exportAll'])->name('assets.export.all');
Route::get('/assets/export/options', [AssetController::class, 'exportOptions'])->name('assets.export.options');


        // IMPORTANT: Specific routes MUST come BEFORE parameterized routes
        Route::get('/assets/assigned', [AssetController::class, 'assigned'])->name('assets.assigned');
        Route::get('/assets/available', [AssetController::class, 'available'])->name('assets.available');
        Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create');
        Route::post('/assets/create', [AssetController::class, 'store'])->name('assets.create');

          Route::post('assets/{slug}/assign', [AssetController::class, 'assign'])->name('assets.assign');
    Route::post('assets/{slug}/return', [AssetController::class, 'returnAsset'])->name('assets.return');
  
        // Parameterized routes come AFTER specific routes
        Route::get('/assets/{asset}', [AssetController::class, 'show'])->name('assets.show');
        Route::get('/assets/{asset}/edit', [AssetController::class, 'edit'])->name('assets.edit');
        Route::post('/assets/{asset}/edit', [AssetController::class, 'update'])->name('assets.update');
        Route::delete('/assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');
        Route::post('/assets/{asset}/assign', [AssetController::class, 'assign'])->name('assets.assign');
        Route::post('/assets/{asset}/audit', [AssetController::class, 'audit'])->name('assets.audit');

        // Asset Attachments
        Route::get('/assets/attachments/{slug}/upload', [AssetController::class, 'assetFilesPage'])->name('assets.attachments');
        Route::post('/assets/attachments/{slug}/upload', [AssetController::class, 'saveAssetFiles'])->name('assets.attachments.upload');
    // Maintenance Management
    Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::get('/maintenance/create', [MaintenanceController::class, 'create'])->name('maintenance.create');
    Route::post('/maintenance/create', [MaintenanceController::class, 'store'])->name('maintenance.store');
    Route::get('/maintenance/scheduled', [MaintenanceController::class, 'scheduled'])->name('maintenance.scheduled');

    Route::get('/maintenance/{maintenance}', [MaintenanceController::class, 'show'])->name('maintenance.show');
    Route::get('/maintenance/{maintenance}/edit', [MaintenanceController::class, 'edit'])->name('maintenance.edit');
    Route::post('/maintenance/{maintenance}/edit', [MaintenanceController::class, 'update'])->name('maintenance.update');
    Route::delete('/maintenance/{maintenance}', [MaintenanceController::class, 'destroy'])->name('maintenance.destroy');
   
    // Assignments Management
    Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
    Route::get('/assignments/create', [AssignmentController::class, 'create'])->name('assignments.create');
    Route::post('/assignments/create', [AssignmentController::class, 'store'])->name('assignments.store');
    Route::get('/assignments/history', [AssignmentController::class, 'history'])->name('assignments.history');

   
    // Location (Enhanced)
    Route::get('/locations', [LocationController::class, 'index'])->name('locations');
    Route::get('/locations/create', [LocationController::class, 'create'])->name('locations.create');
    Route::post('/locations/create', [LocationController::class, 'store'])->name('locations.create');
    Route::get('/locations/{location}/edit', [LocationController::class, 'edit'])->name('locations.edit');
    Route::post('/locations/{location}/edit', [LocationController::class, 'update'])->name('locations.update');
    Route::delete('/locations/{location}', [LocationController::class, 'destroy'])->name('locations.destroy');
    Route::post('/locations/fetch', [LocationController::class, 'fetchLocations']);


    // Courts (Enhanced)
    Route::get('/courts', [CourtController::class, 'index'])->name('courts');
    Route::get('/courts/create', [CourtController::class, 'create'])->name('courts.create');
    Route::post('/courts/create', [CourtController::class, 'store'])->name('courts.create');
        Route::post('/courts/create', [CourtController::class, 'store'])->name('courts.store');
    Route::get('/courts/{court}/edit', [CourtController::class, 'edit'])->name('courts.edit');
    Route::post('/courts/{court}/edit', [CourtController::class, 'update'])->name('courts.update');
    Route::delete('/courts/{court}', [CourtController::class, 'destroy'])->name('courts.destroy');
    Route::get('/courts/{court}', [CourtController::class, 'show'])->name('courts.show');

    Route::get('/users', [UserController::class, 'index'])->name('users');
     Route::get('/users/create', [UserController::class, 'create'])->name('users.create');

    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
     Route::post('/users/create', [UserController::class, 'store'])->name('users.create');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::post('/users/{user}/edit', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
// Add this route to your web.php file
     Route::post('/assets/bulk-delete', [AssetController::class, 'bulkDelete'])->name('assets.bulk-delete');


    // Regions Management
    Route::get('/regions', [RegionController::class, 'index'])->name('regions.index');
    Route::get('/regions/create', [RegionController::class, 'create'])->name('regions.create');
    Route::post('/regions/create', [RegionController::class, 'store'])->name('regions.store');
    Route::get('/regions/{region}/edit', [RegionController::class, 'edit'])->name('regions.edit');
    Route::post('/regions/{region}/edit', [RegionController::class, 'update'])->name('regions.update');
    Route::delete('/regions/{region}', [RegionController::class, 'destroy'])->name('regions.destroy');

    // Reports Routes
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/assets', [ReportController::class, 'assets'])->name('assets');
    Route::get('/users', [ReportController::class, 'users'])->name('users');
    Route::get('/courts', [ReportController::class, 'courts'])->name('courts');
    Route::get('/export-assets', [ReportController::class, 'exportAssetsReport'])->name('export.assets');
    Route::get('/export-users', [ReportController::class, 'exportUsersReport'])->name('export.users');
    Route::get('/export-courts', [ReportController::class, 'exportCourtsReport'])->name('export.courts');
});
    // Reports (Enhanced)
    Route::get('reports', [ReportController::class, 'index'])->name('reports');
    Route::get('reports/assets', [ReportController::class, 'assets'])->name('reports.assets');
    Route::get('reports/maintenance', [ReportController::class, 'maintenance'])->name('reports.maintenance');

// Add these export routes


    // Users (Enhanced)
    Route::get('/admin/system-users', [UserController::class, 'index'])->name('admin.users');
    Route::get('/admin/system-users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/system-users/create', [UserController::class, 'store'])->name('admin.users.create');
    Route::get('/admin/system-users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::post('/admin/system-users/{user}/edit', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/system-users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');



Route::get('/courts/import/form', [CourtController::class, 'importForm'])->name('courts.import.form');
    Route::post('/courts/import', [CourtController::class, 'import'])->name('courts.import.process');
    Route::get('/courts/import/template', [CourtController::class, 'downloadTemplate'])->name('courts.import.template');

     Route::get('/users/import/form', [UserController::class, 'importForm'])->name('users.import.form');
    Route::post('/users/import', [UserController::class, 'import'])->name('users.import.process');
    Route::get('/users/import/template', [UserController::class, 'downloadTemplate'])->name('users.import.template');
    
    // Optional: Export route
    Route::get('/users/export/judges', [UserController::class, 'exportJudges'])->name('users.export.judges');


});

require __DIR__ . '/auth.php';

Route::fallback(function () {
    return abort(404);
});