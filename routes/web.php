<?php

use App\Http\Controllers\admin\AreaController;
use App\Http\Controllers\admin\ColaboradorController;
use App\Http\Controllers\admin\ConfiguracionController;
use App\Http\Controllers\admin\CoordinadorEquipoController;
use App\Http\Controllers\admin\CoordinadorGeneralController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\EstadisticaController;
use App\Http\Controllers\colaborador\ActividadController;
use App\Http\Controllers\coordEquipo\CoordEquipoController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;




Route::get('/', function () {
    return view('public.home.home');
});

Route::get('/auth/login', function () {
    return view('public.auth.login');
})->name('login');

Route::get('/auth/register', function () {
    return view('public.auth.register');
})->name('register');

Route::post('/logout', function () {
    Auth::logout(); 
    return redirect('/');
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

//     Admin
Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
Route::get('/admin/areas', [AreaController::class, 'index'])->name('admin.areas');
Route::get('/admin/colaboradores', [ColaboradorController::class, 'index'])->name('admin.colaboradores');
Route::get('/admin/coordinadores-equipos', [CoordinadorEquipoController::class, 'index' ])->name('admin.coordinadores-equipos');
Route::get('/admin/coordinadores-generales', [CoordinadorGeneralController::class, 'index'])->name('admin.coordinadores-generales');
Route::get('/admin/estadisticas', [EstadisticaController::class, 'index'])->name('admin.estadisticas');
Route::get('/admin/configuracion', [ConfiguracionController::class, 'index'])->name('admin.configuracion');

Route::get('/colaborador/actividades', [ActividadController::class, 'index'])->name('colaborador.actividades');
Route::get('/colaborador/mi-equipo', [\App\Http\Controllers\colaborador\MiEquipoController::class, 'index'])->name('colaborador.mi-equipo');
Route::get('/colaborador/mensajes', [\App\Http\Controllers\colaborador\MensajeController::class, 'index'])->name('colaborador.mensajes');
Route::get('/colaborador/reuniones', [\App\Http\Controllers\colaborador\ReunionController::class, 'index'])->name('colaborador.reuniones');
Route::get('/colaborador/invitaciones', [\App\Http\Controllers\colaborador\InvitacionController::class, 'index'])->name('colaborador.invitaciones');
Route::get('/colaborador/configuracion', [\App\Http\Controllers\colaborador\ConfiguracionController::class, 'index'])->name('colaborador.configuracion');

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




//  Coordinador de Equipo
    Route::get('/coord-equipo/dashboard', [CoordEquipoController::class, 'dashboard'])->name('coord-equipo.dashboard');
    Route::post('/coord-equipo/actividades', [CoordEquipoController::class, 'crearActividad'])->name('coord-equipo.actividades.store');
    Route::post('/coord-equipo/reuniones', [CoordEquipoController::class, 'crearReunion'])->name('coord-equipo.reuniones.store');
    Route::post('/coord-equipo/metas', [CoordEquipoController::class, 'crearMeta'])->name('coord-equipo.metas.store');
    Route::get('/coord-equipo/configuracion', [CoordEquipoController::class, 'dashboard'])->name('coord-equipo.configuracion');


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