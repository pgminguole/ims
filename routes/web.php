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
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\DtsAssignmentController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\AssetImportController;
use App\Http\Controllers\ObsoleteAssetController;
use App\Http\Controllers\RegionalAdminController;
use App\Http\Controllers\Auditor\AuditorReportController;
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
    
    
       Route::prefix('assets/import')->name('assets.import.')->group(function () {
        Route::get('/', [AssetImportController::class, 'index'])->name('index');
        Route::post('/preview', [AssetImportController::class, 'preview'])->name('preview');
        Route::post('/store', [AssetImportController::class, 'import'])->name('store');
    });
    
    
    Route::middleware(['role:auditor'])->prefix('auditor')->name('auditor.')->group(function () {
    // ... your other auditor routes
    
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [AuditorReportController::class, 'index'])->name('index');
        Route::post('/generate', [AuditorReportController::class, 'generate'])->name('generate');
        Route::post('/export', [AuditorReportController::class, 'export'])->name('export');
    });
});

// Add GET route for quick reports
Route::get('auditor/reports/quick-generate', [AuditorReportController::class, 'quickGenerate'])->name('auditor.reports.quick-generate');


    // Auditor Dashboard
    Route::get('/auditor/dashboard', [App\Http\Controllers\Auditor\AuditorController::class, 'dashboard'])->name('auditor.dashboard');
    
    // Auditor Assets
    Route::get('/auditor/assets', [App\Http\Controllers\Auditor\AuditorAssetController::class, 'index'])->name('auditor.assets.index');
    Route::get('/auditor/assets/{asset}', [App\Http\Controllers\Auditor\AuditorAssetController::class, 'show'])->name('auditor.assets.show');
    Route::post('/auditor/assets/{asset}/verify', [App\Http\Controllers\Auditor\AuditorAssetController::class, 'verify'])->name('auditor.assets.verify');
    
    // Auditor Users
    Route::get('/auditor/users', [App\Http\Controllers\Auditor\AuditorUserController::class, 'index'])->name('auditor.users.index');
    Route::get('/auditor/users/{user}', [App\Http\Controllers\Auditor\AuditorUserController::class, 'show'])->name('auditor.users.show');
    
    // Auditor Departments
    Route::get('/auditor/departments', [App\Http\Controllers\Auditor\AuditorDepartmentController::class, 'index'])->name('auditor.departments.index');
    Route::get('/auditor/departments/{department}', [App\Http\Controllers\Auditor\AuditorDepartmentController::class, 'show'])->name('auditor.departments.show');
   
    // Auditor Regions
    Route::get('/auditor/regions', [App\Http\Controllers\Auditor\AuditorRegionController::class, 'index'])->name('auditor.regions.index');
    
    // Auditor Courts
    Route::get('/auditor/courts', [App\Http\Controllers\Auditor\AuditorCourtController::class, 'index'])->name('auditor.courts.index');
    Route::get('/auditor/courts/{court}', [App\Http\Controllers\Auditor\AuditorCourtController::class, 'show'])->name('auditor.courts.show');
    
    // Auditor DTS
    Route::get('/auditor/dts', [App\Http\Controllers\Auditor\AuditorDtsController::class, 'index'])->name('auditor.dts.index');
    Route::get('/auditor/dts/{dts}', [App\Http\Controllers\Auditor\AuditorDtsController::class, 'show'])->name('auditor.dts.show');
    
    // Auditor Reports
    Route::get('/auditor/reports', [App\Http\Controllers\Auditor\AuditorReportController::class, 'index'])->name('auditor.reports.index');
  

    
    
    // Offices Management
Route::middleware(['role:admin'])->group(function () {
    Route::resource('regional-admins', RegionalAdminController::class);
});

Route::get('/offices', [OfficeController::class, 'index'])->name('offices.index');
Route::get('/offices/create', [OfficeController::class, 'create'])->name('offices.create');
Route::post('/offices/create', [OfficeController::class, 'store'])->name('offices.store');
Route::get('/offices/{office}', [OfficeController::class, 'show'])->name('offices.show');
Route::get('/offices/{office}/edit', [OfficeController::class, 'edit'])->name('offices.edit');
Route::put('/offices/{office}/edit', [OfficeController::class, 'update'])->name('offices.update');
Route::delete('/offices/{office}', [OfficeController::class, 'destroy'])->name('offices.destroy');

// Office Asset Assignment Routes
Route::post('/offices/{office}/assign-asset', [OfficeController::class, 'assignAsset'])->name('offices.assign-asset');
Route::delete('/offices/{office}/remove-asset', [OfficeController::class, 'removeAsset'])->name('offices.remove-asset');
    // Categories (Enhanced)
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories/create', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::post('/categories/{category}/edit', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Settings
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/backup', [\App\Http\Controllers\SettingsController::class, 'backup'])->name('settings.backup');
    Route::get('/settings/backup/download', [\App\Http\Controllers\SettingsController::class, 'downloadLatestBackup'])->name('settings.backup.download');
    Route::post('/settings/restore', [\App\Http\Controllers\SettingsController::class, 'restore'])->name('settings.restore');
    Route::post('/settings/wipe', [\App\Http\Controllers\SettingsController::class, 'wipe'])->name('settings.wipe');


    Route::get('/assets/import/form', [AssetController::class, 'importForm'])->name('assets.import.form');
   // Route::post('/assets/import', [AssetController::class, 'import'])->name('assets.import.process');
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
        Route::post('/assets/create', [AssetController::class, 'store'])->name('assets.store');
        
        // Temporary cleanup route
        Route::get('/assets/cleanup', [AssetController::class, 'cleanup'])->name('assets.cleanup');

        Route::post('assets/{asset}/return', [AssetController::class, 'returnAsset'])->name('assets.return');
  
        // Parameterized routes come AFTER specific routes
        Route::get('/assets/{asset}', [AssetController::class, 'show'])->name('assets.show');
        Route::get('/assets/{asset}/edit', [AssetController::class, 'edit'])->name('assets.edit');
        Route::post('/assets/{asset}/edit', [AssetController::class, 'update'])->name('assets.update');
       // Route::post('/assets/{asset}/edit', [AssetController::class, 'update'])->name('assets.update');
        Route::delete('/assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');
        Route::post('/assets/{asset}/assign', [AssetController::class, 'assign'])->name('assets.assign');
        Route::post('/assets/{asset}/audit', [AssetController::class, 'audit'])->name('assets.audit');
        Route::post('/assets/{slug}/change-assigned-date', [AssetController::class, 'changeAssignedDate'])->name('assets.change-assigned-date');
        // Asset Attachments
        Route::get('/assets/attachments/{slug}/upload', [AssetController::class, 'assetFilesPage'])->name('assets.attachments');
        Route::post('/assets/attachments/{slug}/upload', [AssetController::class, 'saveAssetFiles'])->name('assets.attachments.upload');
    // Obsolete Assets Management
    Route::resource('obsolete-assets', ObsoleteAssetController::class);

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
    Route::post('/assignments/create-asset', [AssignmentController::class, 'createAsset'])->name('assignments.create-asset');
    Route::get('/assignments/history', [AssignmentController::class, 'history'])->name('assignments.history');
    
    // Bulk Model Assignment Routes
    Route::get('/assignments/bulk-model', [AssignmentController::class, 'bulkModelAssignment'])
        ->name('assignments.bulk-model');
    Route::post('/assignments/bulk-model', [AssignmentController::class, 'storeBulkModelAssignment'])
        ->name('assignments.bulk-model.store');
    Route::get('/assignments/models/{categoryId}', [AssignmentController::class, 'getModelsByCategory'])
        ->name('assignments.models-by-category');
        
        
        
        // DTS Assignment Routes
Route::prefix('dts-assignments')->name('dts-assignments.')->group(function () {
    Route::get('/', [DtsAssignmentController::class, 'index'])->name('index');
    Route::get('/create', [DtsAssignmentController::class, 'create'])->name('create');
    Route::post('/create', [DtsAssignmentController::class, 'store'])->name('store');
    Route::get('/bulk-create', [DtsAssignmentController::class, 'bulkCreate'])->name('bulk-create');
    Route::post('/bulk-create', [DtsAssignmentController::class, 'storeBulk'])->name('store-bulk');
    Route::get('/{dtsAssignment}', [DtsAssignmentController::class, 'show'])->name('show');
    Route::get('/{dtsAssignment}/edit', [DtsAssignmentController::class, 'edit'])->name('edit');
    Route::post('/{dtsAssignment}/edit', [DtsAssignmentController::class, 'update'])->name('update');
    Route::delete('/{dtsAssignment}', [DtsAssignmentController::class, 'destroy'])->name('destroy');
});

   
    // Location (Enhanced)
    Route::get('/locations', [LocationController::class, 'index'])->name('locations');
    Route::get('/locations/create', [LocationController::class, 'create'])->name('locations.create');
    Route::post('/locations/create', [LocationController::class, 'store'])->name('locations.store');
    Route::get('/locations/{location}/edit', [LocationController::class, 'edit'])->name('locations.edit');
    Route::post('/locations/{location}/edit', [LocationController::class, 'update'])->name('locations.update');
    Route::delete('/locations/{location}', [LocationController::class, 'destroy'])->name('locations.destroy');
    Route::post('/locations/fetch', [LocationController::class, 'fetchLocations']);


    // Courts (Enhanced)
    Route::get('/courts', [CourtController::class, 'index'])->name('courts');
    Route::get('/courts/duplicates', [CourtController::class, 'duplicates'])->name('courts.duplicates');
    Route::get('/courts/create', [CourtController::class, 'create'])->name('courts.create');
    Route::post('/courts/create', [CourtController::class, 'store'])->name('courts.store');
    Route::get('/courts/{court}/edit', [CourtController::class, 'edit'])->name('courts.edit');
    Route::post('/courts/{court}/edit', [CourtController::class, 'update'])->name('courts.update');
    Route::delete('/courts/{court}', [CourtController::class, 'destroy'])->name('courts.destroy');
    Route::get('/courts/{court}', [CourtController::class, 'show'])->name('courts.show');
    
    
// In the courts section, add these routes:
// In the courts section, add these routes:


//"{{route('assets.change-court-assigned-date')}}

// In the courts section, add this route:
Route::post('/courts/{court}/create-asset', [CourtController::class, 'createAsset'])
    ->name('courts.create-asset');

Route::put('/courts/assets/change-date', [CourtController::class, 'changeAssetDate'])
    ->name('assets.change-court-assigned-date');

Route::put('/courts/dts/change-date', [CourtController::class, 'changeDtsDate'])
    ->name('courts.change-dts-date');
    
    Route::put('/dts-assignments/update-date', [DtsAssignmentController::class, 'updateDate'])->name('dts-assignments.update-date');
    
    
Route::prefix('courts/{court}')->group(function () {
    // Store new DTS
    Route::post('/dts', [CourtController::class, 'storeDts'])
        ->name('courts.store-dts');
    
    // Update existing DTS
    Route::put('/dts', [CourtController::class, 'updateDts'])
        ->name('courts.update-dts');
    
    // Remove DTS
    Route::delete('/dts', [CourtController::class, 'removeDts'])
        ->name('courts.remove-dts');
    
    // Assign existing DTS asset
    Route::post('/assign-dts', [CourtController::class, 'assignDts'])
        ->name('courts.assign-dts');
    
    // Assign regular asset
    Route::post('/assign-asset', [CourtController::class, 'assignAsset'])
        ->name('courts.assign-asset');
    
    // Remove asset
    Route::delete('/remove-asset', [CourtController::class, 'removeAsset'])
        ->name('courts.remove-asset');
});




Route::get('/users/duplicates', [UserController::class, 'duplicates'])->name('users.duplicates');
Route::get('/users/fix-roles', [UserController::class, 'fixRolesForm'])->name('users.fix-roles');
Route::post('/users/fix-roles', [UserController::class, 'processFixRoles'])->name('users.fix-roles.process');
Route::post('/users/merge-preview', [UserController::class, 'mergePreview'])->name('users.merge-preview');
Route::post('/users/merge', [UserController::class, 'merge'])->name('users.merge');



    Route::get('/users', [UserController::class, 'index'])->name('users');
     Route::get('/users/create', [UserController::class, 'create'])->name('users.create');

    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
    
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::post('/users/{user}/edit', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
// Add this route to your web.php file
     Route::post('/assets/bulk-delete', [AssetController::class, 'bulkDelete'])->name('assets.bulk-delete');

    Route::get('/api/regions/{region}/locations', [LocationController::class, 'fetchLocations']);



    
        Route::get('/regions/{region}', [RegionController::class, 'show'])->name('regions.show');
    // Regions Management
    Route::get('/regions', [RegionController::class, 'index'])->name('regions.index');
    Route::get('/regions/create', [RegionController::class, 'create'])->name('regions.create');
    Route::post('/regions/create', [RegionController::class, 'store'])->name('regions.store');
    Route::get('/regions/{region}/edit', [RegionController::class, 'edit'])->name('regions.edit');
    Route::post('/regions/{region}/edit', [RegionController::class, 'update'])->name('regions.update');
    Route::delete('/regions/{region}', [RegionController::class, 'destroy'])->name('regions.destroy');
    
    Route::post('/regions/{region}/assign-asset', [RegionController::class, 'assignAsset'])->name('regions.assign-asset');
Route::delete('/regions/{region}/remove-asset', [RegionController::class, 'removeAsset'])->name('regions.remove-asset');
Route::post('/regions/{region}/create-asset', [RegionController::class, 'createAsset'])->name('regions.create-asset');
    


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
    Route::post('/admin/system-users/create', [UserController::class, 'store'])->name('admin.users.store');
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
    
    Route::post('/users/{user}/assets/assign', [UserController::class, 'assignAsset'])
    ->name('users.assets.assign');
Route::post('/users/{user}/assets/remove', [UserController::class, 'removeAsset'])
    ->name('users.assets.remove');
    
    
    Route::prefix('exports')->group(function () {
    // Users exports
    Route::get('/users', [ExportController::class, 'exportUsers'])->name('exports.users');
    
    // Courts exports
    Route::get('/courts', [ExportController::class, 'exportCourts'])->name('exports.courts');
    
    // Offices exports
    Route::get('/offices', [ExportController::class, 'exportOffices'])->name('exports.offices');
    
    // Bulk export with type parameter
    Route::get('/all', [ExportController::class, 'exportAll'])->name('exports.all');
});

// User asset creation routes
Route::post('/users/{user}/create-asset', [UserController::class, 'createAsset'])->name('users.create-asset');

// Office asset creation routes  
Route::post('/offices/{office}/create-asset', [OfficeController::class, 'createAsset'])->name('offices.create-asset');



});

require __DIR__ . '/auth.php';

Route::fallback(function () {
    return abort(404);
});