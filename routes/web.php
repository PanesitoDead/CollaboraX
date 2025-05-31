<?php

use App\Http\Controllers\admin\AreaController;
use App\Http\Controllers\admin\ColaboradorController;
use App\Http\Controllers\admin\ConfiguracionController;
use App\Http\Controllers\admin\CoordinadorEquipoController;
use App\Http\Controllers\admin\CoordinadorGeneralController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\EstadisticaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\colaborador\ActividadController;
use App\Http\Controllers\coordEquipo\ActividadesCoordinadorController;
use App\Http\Controllers\coordEquipo\ConfiguracionCoordinadorController;
use App\Http\Controllers\coordEquipo\CoordEquipoController;
use App\Http\Controllers\coordEquipo\EquipoCoordinadorController;
use App\Http\Controllers\coordEquipo\ReunionesCoordinadorController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;



use App\Http\Controllers\CoordinadorGeneral\ActividadesController;
use App\Http\Controllers\CoordinadorGeneral\DashboarController;
use App\Http\Controllers\CoordinadorGeneral\MetasController;
use App\Http\Controllers\CoordinadorGeneral\ConfigurationController;
use App\Http\Controllers\CoordinadorGeneral\EquiposController;
use App\Http\Controllers\CoordinadorGeneral\ReunionesController;
use App\Http\Controllers\CoordinadorGeneral\MensajesController;
use App\Http\Controllers\superAdmin\EmpresasController;
use App\Http\Controllers\superAdmin\EstadisticaController as SuperAdminEstadisticaController;
use App\Http\Controllers\superAdmin\ConfiguracionController as SuperAdminConfiguracionController;

Route::get('/', function () {
    return view('public.home.home');
})->name('home');

Route::get('/auth/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/auth/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/auth/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/auth/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//     Super Admin
Route::get('/super-admin/dashboard', function () {
    return view('super-admin.dashboard');
})->name('super-admin.dashboard');

Route::get('/super-admin/empresas', [EmpresasController::class, 'index'])->name('super-admin.empresas.index');
Route::get('/super-admin/empresas/{id}', [EmpresasController::class, 'show'])->name('super-admin.empresas.show');
Route::put('/super-admin/empresas/{id}', [EmpresasController::class, 'update'])->name('super-admin.empresas.update');
Route::patch('/super-admin/empresas/{id}/cambiar-estado', [EmpresasController::class, 'cambiarEstado'])->name('super-admin.empresas.cambiar-estado');

Route::get('/super-admin/estadisticas', [SuperAdminEstadisticaController::class, 'index'])->name('super-admin.estadisticas');

Route::get('/super-admin/configuracion', [SuperAdminConfiguracionController::class, 'index'])->name('super-admin.configuracion.index');
Route::put('/super-admin/configuracion/planes/{id}', [SuperAdminConfiguracionController::class, 'update'])->name('super-admin.configuracion.planes.update');


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
    Route::post('/coord-equipo/metas', [CoordEquipoController::class, 'crearMeta'])->name('coord-equipo.metas.store');
    Route::get('/coord-equipo/configuracion', [CoordEquipoController::class, 'dashboard'])->name('coord-equipo.configuracion');

    Route::get('/coord-equipo/equipo', [EquipoCoordinadorController::class, 'index'])->name('coord-equipo.equipo');
    Route::post('/coord-equipo/equipo/invitar', [EquipoCoordinadorController::class, 'invitarColaboradores'])->name('coord-equipo.equipo.invitar');
    Route::delete('/coord-equipo/equipo/invitaciones/{id}', [EquipoCoordinadorController::class, 'cancelarInvitacion'])->name('coord-equipo.equipo.cancelar-invitacion');
    //Route::post('/coord-equipo/equipo/reunion', [EquipoCoordinadorController::class, 'programarReunion'])->name('coord-equipo.equipo.reunion');

    Route::get('/coord-equipo/reuniones', [ReunionesCoordinadorController::class, 'index'])->name('coord-equipo.reuniones');
    Route::post('/coord-equipo/reuniones', [ReunionesCoordinadorController::class, 'store'])->name('coord-equipo.reuniones.store');
    Route::post('/coord-equipo/reuniones/{id}/join', [ReunionesCoordinadorController::class, 'join'])->name('coord-equipo.reuniones.join');
    Route::post('/coord-equipo/reuniones/{id}/cancel', [ReunionesCoordinadorController::class, 'cancel'])->name('coord-equipo.reuniones.cancel');
    Route::post('/coord-equipo/reuniones/{id}/reschedule', [ReunionesCoordinadorController::class, 'reschedule'])->name('coord-equipo.reuniones.reschedule');

    Route::get('/coord-equipo/configuracion', [ConfiguracionCoordinadorController::class, 'index'])->name('coord-equipo.configuracion');
    Route::post('/coord-equipo/configuracion/perfil', [ConfiguracionCoordinadorController::class, 'actualizarPerfil'])->name('coord-equipo.configuracion.perfil');
    Route::post('/coord-equipo/configuracion/notificaciones', [ConfiguracionCoordinadorController::class, 'actualizarNotificaciones'])->name('coord-equipo.configuracion.notificaciones');
    Route::post('/coord-equipo/configuracion/privacidad', [ConfiguracionCoordinadorController::class, 'actualizarPrivacidad'])->name('coord-equipo.configuracion.privacidad');
    Route::post('/coord-equipo/configuracion/password', [ConfiguracionCoordinadorController::class, 'cambiarPassword'])->name('coord-equipo.configuracion.password');
    Route::post('/coord-equipo/configuracion/apariencia', [ConfiguracionCoordinadorController::class, 'actualizarApariencia'])->name('coord-equipo.configuracion.apariencia');

    Route::get('/coord-equipo/actividades', [ActividadesCoordinadorController::class, 'index'])->name('coord-equipo.actividades');

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






// Rutas para Coordinador General - Metas
Route::get('/coordinador-general/metas', [MetasController::class, 'index'])->name('coordinador-general.metas');
Route::get('/coordinador-general/configuracion', [ConfigurationController::class, 'index'])->name('coordinador-general.configuracion');
Route::get('/coordinador-general/dashboard', [DashboarController::class, 'index'])->name('coordinador-general.dashboard');
Route::get('/coordinador-general/equipos', [EquiposController::class, 'index'])->name('coordinador-general.equipos');
Route::get('/coordinador-general/equipos/{id}', [EquiposController::class, 'show'])->name('coordinador-general.equipos.show');
Route::get(url('/coordinador-general/equipos/{id}/edit'), [EquiposController::class, 'edit'])->name('coordinador-general.equipos.edit');

// CRUD DE ACTIVIDADES
Route::get('/coordinador-general/actividades', [ActividadesController::class, 'index'])->name('coordinador-general.actividades');
Route::post('/coordinador-general/actividades', [ActividadesController::class, 'store'])->name('coordinador-general.actividades.store');
Route::delete('/coordinador-general/actividades/{id}', [ActividadesController::class, 'destroy'])->name('coordinador-general.actividades.destroy');
// Rutas API para actividades - CORREGIDAS CON EL PREFIJO
Route::get('/coordinador-general/api/actividades', [ActividadesController::class, 'getActividades'])->name('api.actividades');
Route::get('/coordinador-general/api/actividades/equipos', [ActividadesController::class, 'getEquipos'])->name('api.actividades.equipos');
Route::get('/coordinador-general/actividades/metas-por-equipo/{equipoId}', [ActividadesController::class, 'getMetasPorEquipo'])->name('actividades.metas-por-equipo');
Route::post('/coordinador-general/actividades/actualizar-estado', [ActividadesController::class, 'updateEstado'])->name('actividades.actualizar-estado');
Route::get('/coordinador-general/actividades/metas-por-equipo/{equipoId}', [ActividadesController::class, 'getMetasPorEquipo'])
        ->name('actividades.metas-por-equipo')
        ->where('equipoId', '[0-9]+');

//CRUD DE EQUIPOS
Route::post('/coordinador-general/equipos', [EquiposController::class, 'store'])->name('coordinador-general.equipos.store');
route::put('/coordinador-general/equipos/{id}', [EquiposController::class, 'update'])->name('coordinador-general.equipos.update');
Route::delete('/coordinador-general/equipos/{id}', [EquiposController::class, 'destroy'])->name('coordinador-general.equipos.destroy');


//CRUD DE REUNIONES
Route::get('/coordinador-general/reuniones', [ReunionesController::class, 'index'])->name('coordinador-general.reuniones');
Route::get('/coordinador-general/reuniones', [ReunionesController::class, 'index'])->name('coordinador-general.reuniones');
Route::post('/coordinador-general/reuniones', [ReunionesController::class, 'store'])->name('coordinador-general.reuniones.store');
Route::put('/coordinador-general/reuniones/{id}', [ReunionesController::class, 'update'])->name('coordinador-general.reuniones.update');
Route::delete('/coordinador-general/reuniones/{id}', [ReunionesController::class, 'destroy'])->name('coordinador-general.reuniones.destroy');
Route::post('/coordinador-general/reuniones/{id}/join', [ReunionesController::class, 'join'])->name('coordinador-general.reuniones.join');

//CRUD DE METAS
Route::get('/coordinador-general/metas', [MetasController::class, 'index'])->name('coordinador-general.metas');
route::post('/coordinador-general/metas', [MetasController::class, 'store'])->name('coordinador-general.metas.store');
Route::get('/coordinador-general/metas/{id}', [MetasController::class, 'show'])->name('coordinador-general.metas.show');
Route::get('/coordinador-general/metas/{id}/edit', [MetasController::class, 'edit'])->name('coordinador-general.metas.edit');
Route::put('/coordinador-general/metas/{id}', [MetasController::class, 'update'])->name('coordinador-general.metas.update');
Route::delete('/coordinador-general/metas/{id}', [MetasController::class, 'destroy'])->name('coordinador-general.metas.destroy');

// Rutas de mensajes para coordinador general
Route::get('/coordinador-general/mensajes', [MensajesController::class, 'index'])->name('coordinador-general.mensajes');
Route::get('/coordinador-general/mensajes/{contactId}/messages', [MensajesController::class, 'getMessages'])->name('mensajes.get-messages');
Route::post('/coordinador-general/mensajes/send', [MensajesController::class, 'send'])->name('coordinador-general.mensajes.send');
Route::post('/coordinador-general/mensajes/new-chat', [MensajesController::class, 'newChat'])->name('coordinador-general.mensajes.new-chat');
Route::get('/coordinador-general/mensajes/search', [MensajesController::class, 'search'])->name('coordinador-general.mensajes.search');
Route::post('/coordinador-general/mensajes/mark-read', [MensajesController::class, 'markAsRead'])->name('coordinador-general.mensajes.mark-read');