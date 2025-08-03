# Sistema de Auditoría - CollaboraX

## 📋 Descripción

Se ha implementado un sistema completo de auditoría utilizando el paquete **Spatie Activitylog** para registrar automáticamente todos los cambios realizados en los modelos críticos del sistema.

## ✅ Modelos con Auditoría Activada

Los siguientes modelos ya tienen auditoría configurada:

### 🔐 **Modelos de Usuarios y Autenticación:**
- ✅ **Usuario** - Registra cambios en usuarios (excepto `ultima_conexion` y `en_linea`)
- ✅ **Trabajador** - Registra cambios en trabajadores
- ✅ **Rol** - Registra cambios en roles del sistema

### 🏢 **Modelos de Estructura Organizacional:**
- ✅ **Empresa** - Registra cambios en empresas
- ✅ **Area** - Registra cambios en áreas de la empresa
- ✅ **Equipo** - Registra cambios en equipos de trabajo
- ✅ **AreaCoordinador** - Registra asignaciones de coordinadores a áreas
- ✅ **MiembroEquipo** - Registra cambios en membresías de equipos

### 📋 **Modelos de Gestión de Proyectos:**
- ✅ **Meta** - Registra cambios en metas/objetivos
- ✅ **Tarea** - Registra cambios en tareas (muy importante para seguimiento)

### 👥 **Modelos de Comunicación y Colaboración:**
- ✅ **Mensaje** - Registra envío de mensajes (excepto cambios de `leido`)
- ✅ **Reunion** - Registra cambios en reuniones
- ✅ **Invitacion** - Registra invitaciones a equipos

### 💼 **Modelos de Configuración:**
- ✅ **PlanServicio** - Registra cambios en planes de servicio

## 🔧 Configuración Implementada

### 1. Paquete Instalado
```bash
composer require spatie/laravel-activitylog
```

### 2. Migraciones Ejecutadas
- `activity_log` - Tabla principal de auditoría
- Columnas adicionales para eventos y batch UUID

### 3. Controlador de Auditoría
- **AuditoriaController** con métodos para:
  - Listar auditorías con filtros
  - Ver detalles de una auditoría específica
  - Obtener estadísticas
  - APIs para filtros dinámicos

### 4. Rutas Disponibles
```php
/auditoria                    // Lista de auditorías
/auditoria/show/{id}         // Detalle de auditoría
/auditoria/api/modelos       // Lista de modelos auditados
/auditoria/api/eventos       // Lista de tipos de eventos
/auditoria/api/estadisticas  // Estadísticas de auditoría
```

### 5. Vistas Creadas
- `auditoria/index.blade.php` - Lista con filtros
- `auditoria/show.blade.php` - Detalle completo

## 🎯 Funcionalidades

### Registro Automático
Cada vez que se:
- **Crea** un registro (evento: `created`)
- **Actualiza** un registro (evento: `updated`) 
- **Elimina** un registro (evento: `deleted`)

Se guarda automáticamente:
- Usuario que realizó la acción
- Fecha y hora exacta
- Modelo afectado y su ID
- Valores anteriores y nuevos (en actualizaciones)
- Descripción personalizada del cambio

### Filtros Disponibles
- Por modelo (Usuario, Empresa, Tarea, etc.)
- Por evento (Created, Updated, Deleted)
- Por rango de fechas
- Por usuario específico

### Comandos Artisan

#### Limpiar auditorías antigas
```bash
# Eliminar registros de más de 90 días (por defecto)
php artisan auditoria:limpiar

# Eliminar registros de más de 30 días
php artisan auditoria:limpiar --dias=30
```

#### Seeder de prueba
```bash
# Generar datos de prueba para auditoría
php artisan db:seed --class=AuditoriaTestSeeder
```

## 📊 Ejemplos de Uso

### Ver Auditorías en Código
```php
use Spatie\Activitylog\Models\Activity;

// Últimas 10 actividades
$actividades = Activity::latest()->take(10)->get();

// Actividades de un usuario específico
$actividades = Activity::causedBy($usuario)->get();

// Actividades de un modelo específico
$actividades = Activity::forSubject($empresa)->get();

// Actividades de hoy
$actividades = Activity::whereDate('created_at', today())->get();
```

### Agregar Auditoría a Nuevos Modelos
```php
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class NuevoModelo extends Model
{
    use LogsActivity;
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()                    // Registra campos fillable
            ->logOnlyDirty()                   // Solo cambios reales
            ->dontLogIfAttributesChangedOnly(['campo_ignorar'])
            ->setDescriptionForEvent(fn(string $eventName) => "Nuevo modelo fue {$eventName}");
    }
}
```

## 🔒 Consideraciones de Seguridad

1. **Información Sensible**: Se excluyen automáticamente campos como `clave` y `remember_token`
2. **Limpieza Automática**: Configurar tarea programada para limpiar logs antiguos
3. **Acceso Restringido**: Solo usuarios autenticados pueden ver auditorías

## 📈 Rendimiento

- La auditoría se ejecuta en los eventos de Eloquent, no afecta consultas de lectura
- Los campos excluidos (como `ultima_conexion`) no generan logs innecesarios
- La tabla incluye índices para consultas eficientes

## 🛠️ Configuración Adicional

### Variables de Entorno
```env
# Habilitar/deshabilitar auditoría
ACTIVITY_LOGGER_ENABLED=true

# Nombre personalizado de tabla
ACTIVITY_LOGGER_TABLE_NAME=activity_log
```

### Personalizar Limpieza Automática
En `config/activitylog.php`:
```php
'delete_records_older_than_days' => 365, // Cambiar según necesidad
```

## 🎉 ¡Sistema Listo!

El sistema de auditoría está completamente funcional. Cada cambio en los modelos configurados se registrará automáticamente y podrás ver el historial completo visitando `/auditoria` en tu aplicación.

### Próximos Pasos Recomendados:
1. Ejecutar el seeder de prueba: `php artisan db:seed --class=AuditoriaTestSeeder`
2. Visitar `/auditoria` para ver los logs generados
3. Configurar tarea programada para limpieza automática
4. Añadir auditoría a otros modelos según necesidad
