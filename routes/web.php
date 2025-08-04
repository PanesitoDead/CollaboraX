<?php

use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\ColaboradorController;
use App\Http\Controllers\Admin\ConfiguracionController;
use App\Http\Controllers\Admin\CoordinadorEquipoController;
use App\Http\Controllers\Admin\CoordinadorGeneralController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EstadisticaController;
use App\Http\Controllers\Admin\EstadisticasIngresosController;
use App\Http\Controllers\Admin\SuscripcionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\colaborador\ActividadController;
use App\Http\Controllers\colaborador\InvitacionController;
use App\Http\Controllers\coordEquipo\ActividadesCoordinadorController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\coordEquipo\ConfiguracionCoordinadorController;
use App\Http\Controllers\coordEquipo\CoordEquipoController;
use App\Http\Controllers\coordEquipo\EquipoCoordinadorController;
use App\Http\Controllers\coordEquipo\MetasCoordinadorController;
use App\Http\Controllers\coordEquipo\ReunionesCoordinadorController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\colaborador\ConfiguracionController as ColaboradorConfiguracionController;
use App\Http\Controllers\colaborador\MensajeController;
use App\Http\Controllers\CoordinadorGeneral\ActividadesController;
use App\Http\Controllers\CoordinadorGeneral\DashboarController;
use App\Http\Controllers\CoordinadorGeneral\MetasController;
use App\Http\Controllers\CoordinadorGeneral\ConfigurationController  as CoordinadorConfigurationController;
use App\Http\Controllers\CoordinadorGeneral\EquiposController;
use App\Http\Controllers\CoordinadorGeneral\ReunionesController;
use App\Http\Controllers\CoordinadorGeneral\MensajesController;
use App\Http\Controllers\SuperAdmin\EmpresasController;
use App\Http\Controllers\SuperAdmin\EstadisticaController as SuperAdminEstadisticaController;
use App\Http\Controllers\SuperAdmin\ConfiguracionController as SuperAdminConfiguracionController;
use App\Http\Controllers\SuperAdmin\AuditoriaController as SuperAdminAuditoriaController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\ReportesController as SuperAdminReportesController;
use App\Http\Controllers\AuditoriaController;


// Route::get('/', function () {
//     return 'Aplicación Laravel desplegada correctamente en GCP 🚀';
// });

Route::get('/', function () {
    return view('public.home.home');
})->name('home');

Route::get('/auth/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/auth/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/auth/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/auth/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


//     Super Admin
Route::middleware(['auth', 'role:Super Admin'])->group(function () {
    Route::get('/super-admin/dashboard', [SuperAdminDashboardController::class, 'index'])->name('super-admin.dashboard');
    Route::get('/super-admin/dashboard/api/ingresos', [SuperAdminDashboardController::class, 'apiIngresos'])->name('super-admin.dashboard.api.ingresos');

    Route::get('/super-admin/empresas', [EmpresasController::class, 'index'])->name('super-admin.empresas.index');
    Route::get('/super-admin/empresas/{id}', [EmpresasController::class, 'show'])->name('super-admin.empresas.show');
    Route::put('/super-admin/empresas/{id}', [EmpresasController::class, 'update'])->name('super-admin.empresas.update');
    Route::patch('/super-admin/empresas/{id}/cambiar-estado', [EmpresasController::class, 'cambiarEstado'])->name('super-admin.empresas.cambiar-estado');

    // Rutas para validaciones AJAX de empresas
    Route::post('/super-admin/empresas/validar-campo', [EmpresasController::class, 'validarCampo'])->name('super-admin.empresas.validar-campo');

    Route::get('/super-admin/estadisticas', [SuperAdminEstadisticaController::class, 'index'])->name('super-admin.estadisticas');

    Route::get('/super-admin/configuracion', [SuperAdminConfiguracionController::class, 'index'])->name('super-admin.configuracion.index');

    // Rutas de gestión de planes
    Route::get('/super-admin/configuracion/planes/create', [SuperAdminConfiguracionController::class, 'create'])->name('super-admin.configuracion.planes.create');
    Route::post('/super-admin/configuracion/planes', [SuperAdminConfiguracionController::class, 'store'])->name('super-admin.configuracion.planes.store');
    Route::get('/super-admin/configuracion/planes/{id}', [SuperAdminConfiguracionController::class, 'show'])->name('super-admin.configuracion.planes.show');
    Route::get('/super-admin/configuracion/planes/{id}/edit', [SuperAdminConfiguracionController::class, 'edit'])->name('super-admin.configuracion.planes.edit');
    Route::put('/super-admin/configuracion/planes/{id}', [SuperAdminConfiguracionController::class, 'update'])->name('super-admin.configuracion.planes.update');
    Route::delete('/super-admin/configuracion/planes/{id}', [SuperAdminConfiguracionController::class, 'destroy'])->name('super-admin.configuracion.planes.destroy');

    // Rutas de Auditoría Super Admin
    Route::get('/super-admin/auditoria', [SuperAdminAuditoriaController::class, 'index'])->name('super-admin.auditoria.index');
    Route::get('/super-admin/auditoria/{id}', [SuperAdminAuditoriaController::class, 'show'])->name('super-admin.auditoria.show');
    Route::get('/super-admin/auditoria/api/estadisticas', [SuperAdminAuditoriaController::class, 'estadisticas'])->name('super-admin.auditoria.estadisticas');
    Route::post('/super-admin/auditoria/limpiar', [SuperAdminAuditoriaController::class, 'limpiar'])->name('super-admin.auditoria.limpiar');

    // Rutas de Reportes Super Admin
    Route::get('/super-admin/reportes', [SuperAdminReportesController::class, 'index'])->name('super-admin.reportes.index');
    Route::get('/super-admin/reportes/usuarios', [SuperAdminReportesController::class, 'reporteUsuarios'])->name('super-admin.reportes.usuarios');
    Route::get('/super-admin/reportes/ingresos', [SuperAdminReportesController::class, 'reporteIngresos'])->name('super-admin.reportes.ingresos');
    Route::get('/super-admin/reportes/transacciones', [SuperAdminReportesController::class, 'reporteTransacciones'])->name('super-admin.reportes.transacciones');
    Route::get('/super-admin/reportes/rendimiento-planes', [SuperAdminReportesController::class, 'reporteRendimientoPlanes'])->name('super-admin.reportes.rendimiento-planes');
});


//     Admin
Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard.index');

    Route::get('/admin/areas', [AreaController::class, 'index'])->name('admin.areas.index');
    Route::get('/admin/areas/{id}', [AreaController::class, 'show'])->name('admin.areas.show');
    Route::put('/admin/areas/{id}', [AreaController::class, 'update'])->name('admin.areas.update');
    Route::post('/admin/areas', [AreaController::class, 'store'])->name('admin.areas.store');
    Route::delete('/admin/areas/{id}', [AreaController::class, 'destroy'])->name('admin.areas.destroy');

    // Ruta para validaciones AJAX de áreas
    Route::post('/admin/areas/validar-campo-edicion', [AreaController::class, 'validarCampoEdicion'])->name('validar.campo.area.edicion');

    Route::get('/admin/colaboradores/pag/', [ColaboradorController::class, 'getPaginado'])->name('admin.colaboradores.pag');
    Route::get('/admin/colaboradores', [ColaboradorController::class, 'index'])->name('admin.colaboradores.index');
    Route::post('/admin/colaboradores', [ColaboradorController::class, 'store'])->name('admin.colaboradores.store');
    Route::get('/admin/colaboradores/{id}', [ColaboradorController::class, 'show'])->name('admin.colaboradores.show');
    Route::put('/admin/colaboradores/{id}', [ColaboradorController::class, 'update'])->name('admin.colaboradores.update');
    Route::patch('/admin/colaboradores/{id}/cambiar-estado', [ColaboradorController::class, 'cambiarEstado'])->name('admin.colaboradores.cambiar-estado');

    // Rutas para validaciones AJAX
    Route::post('/admin/colaboradores/validar-correo-personal', [ColaboradorController::class, 'validarCorreoPersonal'])->name('admin.colaboradores.validar-correo-personal');
    Route::post('/admin/colaboradores/validar-correo-corporativo', [ColaboradorController::class, 'validarCorreoCorporativo'])->name('admin.colaboradores.validar-correo-corporativo');
    Route::post('/admin/colaboradores/validar-campo', [ColaboradorController::class, 'validarCampo'])->name('admin.colaboradores.validar-campo');

    // Rutas para validaciones AJAX de edición
    Route::post('/admin/colaboradores/validar-correo-personal-edicion', [ColaboradorController::class, 'validarCorreoPersonalEdicion'])->name('admin.colaboradores.validar-correo-personal-edicion');
    Route::post('/admin/colaboradores/validar-campo-edicion', [ColaboradorController::class, 'validarCampoEdicion'])->name('admin.colaboradores.validar-campo-edicion');

    // Rutas para validaciones AJAX de coordinadores de equipo
    Route::post('/admin/coordinadores-equipos/validar-correo-personal-edicion', [CoordinadorEquipoController::class, 'validarCorreoPersonalEdicion'])->name('validar.correo.personal.coordinador.edicion');
    Route::post('/admin/coordinadores-equipos/validar-campo-edicion', [CoordinadorEquipoController::class, 'validarCampoEdicion'])->name('validar.campo.coordinador.edicion');

    // Rutas para validaciones AJAX de coordinadores generales
    Route::post('/admin/coordinadores-generales/validar-correo-personal-edicion', [CoordinadorGeneralController::class, 'validarCorreoPersonalEdicion'])->name('validar.correo.personal.coordinador.general.edicion');
    Route::post('/admin/coordinadores-generales/validar-campo-edicion', [CoordinadorGeneralController::class, 'validarCampoEdicion'])->name('validar.campo.coordinador.general.edicion');

    Route::get('/admin/coordinadores-equipos', [CoordinadorEquipoController::class, 'index' ])->name('admin.coordinadores-equipos.index');
    Route::get('/admin/coordinadores-equipos/{id}', [CoordinadorEquipoController::class, 'show'])->name('admin.coordinadores-equipos.show');
    Route::put('/admin/coordinadores-equipos/{id}', [CoordinadorEquipoController::class, 'update'])->name('admin.coordinadores-equipos.update');
    Route::patch('/admin/coordinadores-equipos/{id}/cambiar-estado', [CoordinadorEquipoController::class, 'cambiarEstado'])->name('admin.coordinadores-equipos.cambiar-estado');

    Route::get('/admin/coordinadores-generales', [CoordinadorGeneralController::class, 'index'])->name('admin.coordinadores-generales.index');
    Route::get('/admin/coordinadores-generales/{id}', [CoordinadorGeneralController::class, 'show'])->name('admin.coordinadores-generales.show');
    Route::put('/admin/coordinadores-generales/{id}', [CoordinadorGeneralController::class, 'update'])->name('admin.coordinadores-generales.update');
    Route::patch('/admin/coordinadores-generales/{id}/cambiar-estado', [CoordinadorGeneralController::class, 'cambiarEstado'])->name('admin.coordinadores-generales.cambiar-estado');

    Route::get('/admin/estadisticas', [EstadisticaController::class, 'index'])->name('admin.estadisticas');

    // Rutas para estadísticas de ingresos (sólo para super-admin)
    Route::prefix('admin/estadisticas-ingresos')->name('admin.estadisticas-ingresos.')->group(function () {
        Route::get('/', [EstadisticasIngresosController::class, 'index'])->name('index');
        Route::get('/api/por-mes', [EstadisticasIngresosController::class, 'getIngresosPorMes'])->name('api.por-mes');
        Route::get('/api/mes-actual', [EstadisticasIngresosController::class, 'getIngresosMesActual'])->name('api.mes-actual');
        Route::get('/api/generales', [EstadisticasIngresosController::class, 'getEstadisticasGenerales'])->name('api.generales');
        Route::get('/api/por-planes', [EstadisticasIngresosController::class, 'getEstadisticasPorPlanes'])->name('api.por-planes');
        Route::get('/api/por-rango', [EstadisticasIngresosController::class, 'getIngresosPorRango'])->name('api.por-rango');
        Route::get('/api/graficos', [EstadisticasIngresosController::class, 'getDatosParaGraficos'])->name('api.graficos');
        Route::get('/api/resumen', [EstadisticasIngresosController::class, 'getResumenCompleto'])->name('api.resumen');
    });

    Route::get('/admin/configuracion', [ConfiguracionController::class, 'index'])->name('admin.configuracion.index');

    // Rutas para configuración de empresa
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::put('/empresa', [ConfiguracionController::class, 'updateEmpresa'])->name('empresa.update');
        Route::put('/configuracion/empresa', [ConfiguracionController::class, 'updateEmpresa'])->name('configuracion.update-empresa');
        Route::put('/password', [ConfiguracionController::class, 'updatePassword'])->name('password.update');
        Route::put('/configuracion/password', [ConfiguracionController::class, 'updatePassword'])->name('configuracion.update-password');
    });

    // Rutas para suscripciones (Proxy a API externa)
    Route::prefix('admin/suscripciones')->name('admin.suscripciones.')->group(function () {
        // Datos principales para la vista
        Route::get('/datos', [SuscripcionController::class, 'obtenerDatosSuscripcion'])->name('datos');
        Route::get('/resumen', [SuscripcionController::class, 'obtenerResumenCompleto'])->name('resumen');
        Route::get('/actual', [SuscripcionController::class, 'obtenerSuscripcionActual'])->name('actual');
        
        // Gestión de planes
        Route::get('/planes', [SuscripcionController::class, 'obtenerPlanes'])->name('planes');
        
        // Gestión de pagos
        Route::post('/crear-preferencia', [SuscripcionController::class, 'crearPreferenciaPago'])->name('crear-preferencia');
        Route::get('/historial', [SuscripcionController::class, 'obtenerHistorialPagos'])->name('historial');
        Route::get('/pagos/{id}/estado', [SuscripcionController::class, 'verificarEstadoPago'])->name('pago.estado');
        Route::get('/pagos/{id}/comprobante', [SuscripcionController::class, 'descargarComprobante'])->name('pago.comprobante');
        
        // Gestión de suscripciones
        Route::post('/cancelar', [SuscripcionController::class, 'cancelarSuscripcion'])->name('cancelar');
        Route::post('/renovar', [SuscripcionController::class, 'renovarSuscripcion'])->name('renovar');
        Route::post('/cancelar-renovacion', [SuscripcionController::class, 'cancelarRenovacionAutomatica'])->name('cancelar-renovacion');
        Route::post('/activar-renovacion', [SuscripcionController::class, 'activarRenovacionAutomatica'])->name('activar-renovacion');
        Route::post('/cambiar-renovacion', [SuscripcionController::class, 'cambiarRenovacionAutomatica'])->name('cambiar-renovacion');
        Route::get('/obtener-resumen-completo', [SuscripcionController::class, 'obtenerResumenCompleto'])->name('obtener-resumen-completo');
        Route::post('/limpiar-cache', [SuscripcionController::class, 'limpiarCache'])->name('limpiar-cache');
        
        // Métodos administrativos
        Route::post('/verificar-vencidas', [SuscripcionController::class, 'verificarSuscripcionesVencidas'])->name('verificar-vencidas');
    });
});

// Webhook para recibir notificaciones de la API externa (sin middleware de autenticación)
Route::post('/webhook/pagos', [SuscripcionController::class, 'webhookPago'])->name('webhook.pagos');

// Rutas para el colaborador
Route::middleware(['auth', 'role:Colaborador'])->group(function () {
    Route::get('/colaborador/actividades', [ActividadController::class, 'index'])->name('colaborador.actividades');
    Route::get('/colaborador/actividades/{id}', [ActividadController::class, 'show'])->name('colaborador.actividades.show');
    Route::get('/colaborador/mi-equipo', [\App\Http\Controllers\colaborador\MiEquipoController::class, 'index'])->name('colaborador.mi-equipo');

    Route::get('/colaborador/invitaciones/{estado?}', [InvitacionController::class, 'index'])->name('colaborador.invitaciones.index')->where('estado', 'pendiente|historial')->defaults('estado', 'pendiente');
    Route::patch('/colaborador/invitaciones/{id}/aceptar', [InvitacionController::class, 'aceptar'])->name('colaborador.invitaciones.aceptar');
    Route::patch('/colaborador/invitaciones/{id}/rechazar', [InvitacionController::class, 'rechazar'])->name('colaborador.invitaciones.rechazar');

    // Rutas para los mensajes del colaborador
    Route::get('/colaborador/mensajes', [MensajeController::class, 'index'])->name('colaborador.mensajes');
    Route::post('/colaborador/search-workers', [MensajeController::class, 'searchWorkers'])->name('colaborador.mensajes.search-workers');
    Route::get('/colaborador/get-messages/{contactId}', [MensajeController::class, 'getMessages'])->name('colaborador.mensajes.get-messages');
    Route::post('/colaborador/mensajes/send', [MensajeController::class, 'send'])->name('colaborador.mensajes.send');
    Route::post('/colaborador/mensajes/new-chat', [MensajeController::class, 'newChat'])->name('colaborador.mensajes.new-chat');
    Route::post('/colaborador/mensajes/mark-as-read', [MensajeController::class, 'markAsRead'])->name('colaborador.mensajes.mark-as-read');
    Route::post('/colaborador/mensajes/search', [MensajeController::class, 'search'])->name('colaborador.mensajes.search');

    Route::get('/colaborador/reuniones', [\App\Http\Controllers\colaborador\ReunionController::class, 'index'])->name('colaborador.reuniones');

    // Rutas para la configuración del colaborador
    Route::get('/colaborador/configuracion', [ColaboradorConfiguracionController::class, 'index'])->name('colaborador.configuracion.index');
    Route::post('/colaborador/configuracion/profile', [ColaboradorConfiguracionController::class, 'updateProfile'])->name('colaborador.configuracion.update-profile');
    Route::post('/colaborador/configuracion/photo', [ColaboradorConfiguracionController::class, 'uploadPhoto'])->name('colaborador.configuracion.upload-photo');
    Route::post('/colaborador/configuracion/notifications', [ColaboradorConfiguracionController::class, 'updateNotifications'])->name('colaborador.configuracion.update-notifications');
    Route::post('/colaborador/configuracion/security', [ColaboradorConfiguracionController::class, 'updateSecurity'])->name('colaborador.configuracion.update-security');
});



//  Coordinador de Equipo
Route::middleware(['auth', 'role:Coord. Equipo'])->group(function () {
    Route::get('/coord-equipo/dashboard', [CoordEquipoController::class, 'dashboard'])->name('coord-equipo.dashboard');

    Route::get('/coord-equipo/equipo', [EquipoCoordinadorController::class, 'index'])->name('coord-equipo.equipo');
    Route::post('/coord-equipo/equipo/invitar', [EquipoCoordinadorController::class, 'invitarColaboradores'])->name('coord-equipo.equipo.invitar');
    Route::delete('/coord-equipo/equipo/invitaciones/{id}', [EquipoCoordinadorController::class, 'cancelarInvitacion'])->name('coord-equipo.equipo.cancelar-invitacion');

    Route::get('/coord-equipo/reuniones', [ReunionesCoordinadorController::class, 'index'])->name('coord-equipo.reuniones');
    Route::post('/coord-equipo/reuniones', [ReunionesCoordinadorController::class, 'store'])->name('coord-equipo.reuniones.store');
    Route::post('/coord-equipo/reuniones/{id}/join', [ReunionesCoordinadorController::class, 'join'])->name('coord-equipo.reuniones.join');
    Route::post('/coord-equipo/reuniones/{id}/cancel', [ReunionesCoordinadorController::class, 'cancel'])->name('coord-equipo.reuniones.cancel');
    Route::post('/coord-equipo/reuniones/{id}/reschedule', [ReunionesCoordinadorController::class, 'reschedule'])->name('coord-equipo.reuniones.reschedule');

    Route::get('/coord-equipo/configuracion', [ConfiguracionCoordinadorController::class, 'index'])->name('coord-equipo.configuracion');
    Route::post('/coord-equipo/configuracion/perfil', [ConfiguracionCoordinadorController::class, 'actualizarPerfil'])->name('coord-equipo.configuracion.perfil');
    Route::post('/coord-equipo/configuracion/password', [ConfiguracionCoordinadorController::class, 'cambiarPassword'])->name('coord-equipo.configuracion.password');

    Route::get('/coord-equipo/actividades', [ActividadesCoordinadorController::class, 'index'])->name('coord-equipo.actividades');
    Route::post('/coord-equipo/actividades', [ActividadesCoordinadorController::class, 'storeActividad'])->name('coord-equipo.actividades.store');
    Route::put('/coord-equipo/actividades/{id}/actualizar', [ActividadesCoordinadorController::class, 'actualizarActividad'])->name('coord-equipo.actividades.actualizar');

    Route::get('/coord-equipo/metas', [MetasCoordinadorController::class, 'index'])->name('coord-equipo.metas');
    Route::post('/coord-equipo/metas', [MetasCoordinadorController::class, 'store'])->name('coord-equipo.metas.store');
    Route::put('/coord-equipo/metas/{id}', [MetasCoordinadorController::class, 'update'])->name('coord-equipo.metas.update');
    Route::delete('/coord-equipo/metas/{id}', [MetasCoordinadorController::class, 'destroy'])->name('coord-equipo.metas.destroy');

    //API
    Route::get('/coord-equipo/api/actividades/equipo', [ActividadesCoordinadorController::class, 'actividadesPorEquipo'])->name('api.coord-equipo.actividades.equipo');
    Route::get('/coord-equipo/api/estados', [ActividadesCoordinadorController::class, 'estados'])->name('api.coord-equipo.estados');
    Route::get('/coord-equipo/api/metas/equipo', [ActividadesCoordinadorController::class, 'metasPorEquipo'])->name('api.coord-equipo.metas.equipo');
    Route::post('/coord-equipo/api/actividades/{id}/cambiar-estado', [ActividadesCoordinadorController::class, 'actualizarEstadoActividad'])->name('api.coord-equipo.actividades.estado');
    Route::post('/coord-equipo/api/actividades/crear', [ActividadesCoordinadorController::class, 'crearActividad'])->name('api.coord-equipo.actividades.crear');
});

Route::middleware('auth')->group(function () {
    
    // Rutas de Auditoría
    Route::prefix('auditoria')->name('auditoria.')->group(function () {
        Route::get('/', [AuditoriaController::class, 'index'])->name('index');
        Route::get('/show/{id}', [AuditoriaController::class, 'show'])->name('show');
        Route::get('/api/modelos', [AuditoriaController::class, 'getModelosDisponibles'])->name('api.modelos');
        Route::get('/api/eventos', [AuditoriaController::class, 'getEventosDisponibles'])->name('api.eventos');
        Route::get('/api/estadisticas', [AuditoriaController::class, 'estadisticas'])->name('api.estadisticas');
    });
    
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






// Rutas para Coordinador General
Route::middleware(['auth', 'role:Coord. General'])->group(function () {
    Route::get('/coordinador-general/metas', [MetasController::class, 'index'])->name('coordinador-general.metas');
    Route::get('/coordinador-general/dashboard', [DashboarController::class, 'index'])->name('coordinador-general.dashboard');

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
    Route::get('/coordinador-general/equipos', [EquiposController::class, 'index'])->name('coordinador-general.equipos');
    Route::post('/coordinador-general/equipos', [EquiposController::class, 'store'])->name('coordinador-general.equipos.store');
     // Rutas AJAX (ANTES de las rutas con parámetros)
    Route::get('/coordinador-general/equipos/buscar-colaboradores', [EquiposController::class, 'buscarColaboradores'])->name('buscar-colaboradores');
    Route::get('/coordinador-general/equipos/{id}', [EquiposController::class, 'show'])->name('coordinador-general.equipos.show');
    Route::delete('/coordinador-general/equipos/{id}', [EquiposController::class, 'destroy'])->name('coordinador-general.equipos.destroy');
    route::post('coordinador-general/equipos/{id}/agregar-miembro', [EquiposController::class, 'agregarMiembro'])->name('coordinador-general.equipos.agregar-miembro');
    route::post('coordinador-general/equipos/{id}/eliminar-miembro', [EquiposController::class, 'eliminarMiembro'])->name('coordinador-general.equipos.eliminar-miembro');
    route::post('coordinador-general/equipos/{id}/editar-miembro', [EquiposController::class, 'editarMiembro'])->name('coordinador-general.equipos.editar-miembro');
    route::post('coordinador-general/equipos/{id}/miembros', [EquiposController::class, 'getMiembros'])->name('coordinador-general.equipos.miembros');

    //CRUD DE REUNIONES
    Route::get('/coordinador-general/reuniones', [ReunionesController::class, 'index'])->name('coordinador-general.reuniones');
    Route::post('/coordinador-general/reuniones', [ReunionesController::class, 'store'])->name('coordinador-general.reuniones.store');
    Route::put('/coordinador-general/reuniones/{id}', [ReunionesController::class, 'update'])->name('coordinador-general.reuniones.update');
    Route::delete('/coordinador-general/reuniones/{id}', [ReunionesController::class, 'destroy'])->name('coordinador-general.reuniones.destroy');
    Route::post('/coordinador-general/reuniones/{id}/join', [ReunionesController::class, 'join'])->name('coordinador-general.reuniones.join');

    //CRUD DE METAS
    Route::get('/coordinador-general/metas', [MetasController::class, 'index'])->name('coordinador-general.metas');
    route::post('/coordinador-general/metas', [MetasController::class, 'store'])->name('coordinador-general.metas.store');
    Route::get('/coordinador-general/metas/{id}', [MetasController::class, 'show'])->name('coordinador-general.metas.show');
    Route::put('/coordinador-general/metas/{id}', [MetasController::class, 'update'])->name('coordinador-general.metas.update');
    Route::delete('/coordinador-general/metas/{id}', [MetasController::class, 'destroy'])->name('coordinador-general.metas.destroy');
    route::get('/coordinador-general/metas/equipos', [MetasController::class, 'getEquipos'])->name('coordinador-general.metas.equipos');
    Route::post('/coordinador-general/actividades/actualizar-estado', [ActividadesController::class, 'updateEstado'])->name('coordinador-general.actividades.actualizar-estado');

    Route::get('/coordinador-general/configuracion', [CoordinadorConfigurationController::class, 'index'])->name('coordinador-general.configuracion');
    Route::post('/coordinador-general/configuracion/profile', [CoordinadorConfigurationController::class, 'updateProfile'])->name('coordinador-general.configuracion.update-profile');
    Route::post('/coordinador-general/configuracion/photo', [CoordinadorConfigurationController::class, 'uploadPhoto'])->name('coordinador-general.configuracion.upload-photo');
    Route::post('/coordinador-general/configuracion/notifications', [CoordinadorConfigurationController::class, 'updateNotifications'])->name('coordinador-general.configuracion.update-notifications');
    Route::post('/coordinador-general/configuracion/security', [CoordinadorConfigurationController::class, 'updateSecurity'])->name('coordinador-general.configuracion.update-security');

    // Mensajes - RUTAS COMPLETAS
    Route::get('/coordinador-general/mensajes', [MensajesController::class, 'index'])->name('coordinador-general.mensajes');
    Route::post('/coordinador-general/mensajes/search-workers', [MensajesController::class, 'searchWorkers'])->name('coordinador-general.mensajes.search-workers');
    Route::get('/coordinador-general/mensajes/get-messages/{contactId}', [MensajesController::class, 'getMessages'])->name('coordinador-general.mensajes.get-messages');
    Route::post('/coordinador-general/mensajes/send', [MensajesController::class, 'send'])->name('coordinador-general.mensajes.send');
    Route::post('/coordinador-general/mensajes/new-chat', [MensajesController::class, 'newChat'])->name('coordinador-general.mensajes.new-chat');
    Route::post('/coordinador-general/mensajes/mark-as-read', [MensajesController::class, 'markAsRead'])->name('coordinador-general.mensajes.mark-as-read');
    Route::post('/coordinador-general/mensajes/search', [MensajesController::class, 'search'])->name('coordinador-general.mensajes.search');
    Route::post('/coordinador-general/mensajes/store-fcm-token', [MensajesController::class, 'storeFcmToken'])->name('coordinador-general.mensajes.store-fcm-token');
});

// ===============================================
// RUTAS DE PRUEBA PARA MIDDLEWARE (TEMPORALES)
// ===============================================
Route::middleware(['auth'])->group(function () {
    // Página principal de prueba de middleware
    Route::get('/test-middleware', function () {
        $user = Auth::user();
        
        // Obtener rol del usuario
        $userRole = null;
        if ($user && isset($user->correo)) {
            $userWithRole = DB::table('usuarios')
                ->join('roles', 'usuarios.rol_id', '=', 'roles.id')
                ->where('usuarios.correo', $user->correo)
                ->select('roles.nombre as rol_nombre')
                ->first();
            
            if ($userWithRole) {
                $userRole = $userWithRole->rol_nombre;
            }
        }
        
        return view('test-middleware', compact('userRole'));
    })->name('test.middleware');

    // Rutas de prueba específicas por rol
    Route::middleware(['role:Super Admin'])->get('/test-middleware/super-admin', function () {
        return view('test-middleware', [
            'userRole' => 'Super Admin',
            'requiredRole' => 'Super Admin',
            'message' => '¡Acceso autorizado como Super Admin!'
        ]);
    })->name('test.middleware.super-admin');

    Route::middleware(['role:Admin'])->get('/test-middleware/admin', function () {
        return view('test-middleware', [
            'userRole' => 'Admin',
            'requiredRole' => 'Admin',
            'message' => '¡Acceso autorizado como Admin!'
        ]);
    })->name('test.middleware.admin');

    Route::middleware(['role:Coord. General'])->get('/test-middleware/coord-general', function () {
        return view('test-middleware', [
            'userRole' => 'Coord. General',
            'requiredRole' => 'Coord. General',
            'message' => '¡Acceso autorizado como Coordinador General!'
        ]);
    })->name('test.middleware.coord-general');

    Route::middleware(['role:Coord. Equipo'])->get('/test-middleware/coord-equipo', function () {
        return view('test-middleware', [
            'userRole' => 'Coord. Equipo',
            'requiredRole' => 'Coord. Equipo',
            'message' => '¡Acceso autorizado como Coordinador de Equipo!'
        ]);
    })->name('test.middleware.coord-equipo');

    Route::middleware(['role:Colaborador'])->get('/test-middleware/colaborador', function () {
        return view('test-middleware', [
            'userRole' => 'Colaborador',
            'requiredRole' => 'Colaborador',
            'message' => '¡Acceso autorizado como Colaborador!'
        ]);
    })->name('test.middleware.colaborador');
});