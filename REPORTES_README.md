# Módulo de Reportes - Super Admin

## Descripción
Se ha implementado un módulo completo de reportes para el Super Admin que permite generar y exportar diferentes tipos de reportes del sistema, incluyendo la integración con la API de pagos de MercadoPago para reportes de ingresos.

## Características Implementadas

### 1. Sección de Reportes en el Dashboard
- Nueva opción "Reportes" agregada al sidebar del Super Admin
- Icono: `file-text` (Lucide Icons)
- Ruta: `/super-admin/reportes`

### 2. Tipos de Reportes Disponibles

#### 2.1 Reporte de Usuarios
- **Descripción**: Lista detallada de todos los usuarios del sistema
- **Filtros**: Período (mes actual, últimos 3/6 meses, año actual, todos los tiempos)
- **Información incluida**:
  - ID, nombre, correo, rol, empresa
  - Estado (activo/inactivo)
  - Fecha de registro y última conexión
- **Exportación**: Vista web y PDF

#### 2.2 Reporte de Ingresos (Integración API MercadoPago)
- **Descripción**: Análisis detallado de ingresos usando la API de pagos
- **Parámetros disponibles**:
  - `formato`: tabla, resumen, detallado
  - `periodo`: mes-actual, ultimos-3-meses, ultimos-6-meses, anual, personalizado
  - `agrupacion`: fecha, plan, estado, mes
  - `fecha_inicio` / `fecha_fin`: Para período personalizado
  - `plan_id`: Filtrar por plan específico
  - `estado`: pending, approved, rejected
- **Endpoint**: `GET /api/reportes/ingresos`
- **Exportación**: Vista web y PDF

#### 2.3 Reporte de Transacciones
- **Descripción**: Listado detallado de transacciones para exportación
- **Parámetros**:
  - `limite`: Número máximo de transacciones (10, 25, 50, 100, 250)
- **Endpoint**: `GET /api/reportes/transacciones`
- **Exportación**: Vista web y PDF

#### 2.4 Reporte de Rendimiento de Planes
- **Descripción**: Análisis completo del rendimiento de todos los planes
- **Métricas incluidas**:
  - Tasas de conversión
  - Ingresos por plan
  - Usuarios únicos
  - Análisis de rendimiento temporal
- **Endpoint**: `GET /api/reportes/rendimiento-planes`
- **Exportación**: Vista web y PDF

### 3. Funcionalidades Técnicas

#### 3.1 Controlador: `ReportesController`
- **Ubicación**: `app/Http/Controllers/superAdmin/ReportesController.php`
- **Métodos principales**:
  - `index()`: Vista principal de reportes
  - `reporteUsuarios()`: Reporte de usuarios con filtros
  - `reporteIngresos()`: Reporte de ingresos (API MercadoPago)
  - `reporteTransacciones()`: Reporte de transacciones
  - `reporteRendimientoPlanes()`: Reporte de rendimiento

#### 3.2 Integración con API de Pagos
- **URL Base**: `http://34.173.216.37:3000/api`
- **Timeout**: 30 segundos
- **Manejo de errores**: Datos de respaldo en caso de fallo de API
- **Cliente HTTP**: GuzzleHttp

#### 3.3 Generación de PDFs
- **Librería**: `barryvdh/laravel-dompdf`
- **Plantillas PDF**: `resources/views/super-admin/reportes/pdf/`
  - `usuarios.blade.php`
  - `ingresos.blade.php`
  - `transacciones.blade.php`
  - `rendimiento.blade.php`

### 4. Rutas Implementadas

```php
// Vista principal
GET /super-admin/reportes

// APIs de reportes
GET /super-admin/reportes/usuarios
GET /super-admin/reportes/ingresos
GET /super-admin/reportes/transacciones
GET /super-admin/reportes/rendimiento-planes
```

### 5. Vista Principal

#### 5.1 Diseño
- **Layout**: Grid responsivo con tarjetas para cada tipo de reporte
- **Controles**: Filtros específicos para cada reporte
- **Modal**: Visualización de reportes en pantalla
- **Loading**: Indicador de carga durante generación

#### 5.2 Características de UX
- **Resumen básico**: Estadísticas rápidas del sistema
- **Filtros dinámicos**: Fechas personalizadas para reportes de ingresos
- **Exportación directa**: Botones para descargar PDFs
- **Visualización en modal**: Para revisar antes de descargar

### 6. Manejo de Errores y Respaldos

#### 6.1 Datos de Respaldo
- Si la API de MercadoPago no está disponible, se usan datos de demostración
- Mensajes informativos cuando se usan datos de respaldo
- Logs de errores para depuración

#### 6.2 Validaciones
- Validación de fechas para períodos personalizados
- Manejo de respuestas vacías de la API
- Timeouts configurables para requests externos

### 7. Estilos y Diseño

#### 7.1 Paleta de Colores por Reporte
- **Usuarios**: Azul (`#2563eb`)
- **Ingresos**: Verde (`#059669`)
- **Transacciones**: Púrpura (`#7c3aed`)
- **Rendimiento**: Amarillo (`#f59e0b`)

#### 7.2 Componentes UI
- Cards con iconos Lucide
- Tablas responsivas
- Estados de carga animados
- Modales para visualización

## Instalación y Configuración

### 1. Dependencias
```bash
composer require barryvdh/laravel-dompdf
```

### 2. Configuración de dompdf
```bash
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

### 3. Variables de Entorno
```env
PAGOS_MICROSERVICE_URL=http://34.173.216.37:3000/api
```

## Uso

### 1. Acceso
- Ingresar como Super Admin
- Navegar a "Reportes" en el sidebar
- Seleccionar el tipo de reporte deseado

### 2. Generar Reportes
- Configurar filtros según el tipo de reporte
- Hacer clic en "Ver Reporte" para visualizar
- Hacer clic en "Descargar PDF" para exportar

### 3. Ejemplos de API de Ingresos

```bash
# Reporte por fecha del mes actual
GET /super-admin/reportes/ingresos

# Reporte por planes del último trimestre
GET /super-admin/reportes/ingresos?agrupacion=plan&periodo=ultimos-3-meses

# Reporte personalizado con fechas
GET /super-admin/reportes/ingresos?periodo=personalizado&fecha_inicio=2025-07-01&fecha_fin=2025-07-31
```

## Archivos Modificados/Creados

### Nuevos Archivos
- `app/Http/Controllers/superAdmin/ReportesController.php`
- `resources/views/super-admin/reportes/index.blade.php`
- `resources/views/super-admin/reportes/pdf/usuarios.blade.php`
- `resources/views/super-admin/reportes/pdf/ingresos.blade.php`
- `resources/views/super-admin/reportes/pdf/transacciones.blade.php`
- `resources/views/super-admin/reportes/pdf/rendimiento.blade.php`

### Archivos Modificados
- `routes/web.php` - Agregadas rutas de reportes
- `resources/views/components/super-admin-sidebar.blade.php` - Agregada opción de reportes
- `composer.json` - Agregada dependencia de dompdf

## Consideraciones de Rendimiento

1. **Cache**: Los reportes no usan cache actualmente para mostrar datos en tiempo real
2. **Paginación**: Los PDFs manejan saltos de página automáticos
3. **Timeouts**: API calls tienen timeout de 30 segundos
4. **Límites**: Reportes de transacciones permiten limitar resultados

## Extensibilidad

El módulo está diseñado para ser fácilmente extensible:
- Agregar nuevos tipos de reportes en el controlador
- Crear nuevas plantillas PDF
- Integrar con otras APIs
- Añadir filtros adicionales
