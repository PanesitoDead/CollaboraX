<?php

use App\Http\Controllers\admin\ColaboradorController;
use App\Http\Controllers\admin\DashboardController;
use Illuminate\Support\Facades\Route;




Route::get('/', function () {
    return view('public.home.home');
});

Route::get('/auth/login', function () {
    return view('public.auth.login');
})->name('login');

Route::get('/auth/register', function () {
    return view('public.auth.register');
})->name('register');

Route::get('/logout', function () {
    return view('public.auth.logout');
})->name('logout');

//     Super Admin
Route::get('/super-admin/dashboard', function () {
    return view('super-admin.dashboard');
})->name('super-admin.dashboard');

Route::get('/super-admin/empresas', function () {
    return view('super-admin.empresas');
})->name('super-admin.empresas');

Route::get('/super-admin/estadisticas', function () {
    return view('super-admin.estadisticas');
})->name('super-admin.estadisticas');

Route::get('/super-admin/configuracion', function () {
    return view('super-admin.configuracion');
})->name('super-admin.configuracion');


Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
Route::get('/admin/colaboradores', [ColaboradorController::class, 'index'])->name('admin.colaboradores');


// Route::resource('areas', AreaController::class);
// Route::resource('coordinadores-equipo', CoordinadorEquipoController::class);
// Route::resource('coordinadores-generales', CoordinadorGeneralController::class);

// Route::get('estadisticas', [EstadisticaController::class, 'index'])->name('estadisticas');
// Route::get('configuracion', [ConfiguracionController::class, 'index'])->name('configuracion');


// Route::get('/dashboard-admin', [DashboardController::class, 'admin'])
// ->middleware(['auth', 'verified', CheckAdmin::class])->name('dashboard.admin');

// Route::get('/dashboard-user', [DashboardController::class, 'user'])
// ->middleware(['auth', 'verified', CheckUser::class])->name('dashboard.user');

// Route::get('/prontuario/{id}/view', [ProntuarioController::class, 'viewProntuarioPdf'])->name('prontuario.view');


Route::middleware('auth')->group(function () {
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route::get('/prontuario/index/{slug}', [ProntuarioController::class,'index'])->name('prontuario');
    // Route::get( '/prontuario/create', [ProntuarioController::class,'create'])->name('prontuario.create');
    // Route::get( '/prontuario/create/{slug}', [ProntuarioController::class,'createByType'])->name('prontuario.create.bytype');
    // Route::post('/prontuario', [ProntuarioController::class,'store'])->name('prontuario.store');
    // //Route::get('/prontuario/show/{id}', [ProntuarioController::class,'show'])->name('prontuario.show');
    // Route::get('/prontuario/show/{slug}/{id}', [ProntuarioController::class, 'showByType'])->name('prontuario.show');
    // Route::delete('/prontuario/{id}', [ProntuarioController::class, 'destroy'])->name('prontuario.destroy');
    // Route::get('/prontuario/initial-numbers', [ProntuarioController::class,'initialNumbers'])->name('prontuario.initial.numbers');
    // Route::post('/prontuario/initial-numbers/store', [ProntuarioController::class, 'storeInitialNumber'])->name('prontuario.initial.store');

    // Route::get('/report', [PDFGeneratorController::class, 'index'])->name('report');
    // Route::get('/report-user/{id}', [PDFGeneratorController::class, 'generateByWorker'])->name('report.user');
    // Route::get('/report-admin', [PDFGeneratorController::class, 'generateAdminReports'])->name('report.admin');
    // Route::get('/export-excel', [PDFGeneratorController::class, 'exportByAdmin'])->name('export.admin');
    // Route::get('/export-excel/{id}', [PDFGeneratorController::class, 'exportByWorker'])->name('export.user');  
    
    
});