@extends('layouts.app')

@section('title', 'Auditoría del Sistema')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-search"></i>
                        Auditoría del Sistema
                    </h3>
                </div>
                
                <div class="card-body">
                    <!-- Filtros -->
                    <form method="GET" action="{{ route('auditoria.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="modelo" class="form-label">Modelo</label>
                                <select name="modelo" id="modelo" class="form-control">
                                    <option value="">Todos los modelos</option>
                                    @foreach(\Spatie\Activitylog\Models\Activity::select('subject_type')->distinct()->pluck('subject_type')->filter() as $modelo)
                                        <option value="{{ $modelo }}" {{ request('modelo') == $modelo ? 'selected' : '' }}>
                                            {{ class_basename($modelo) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label for="evento" class="form-label">Evento</label>
                                <select name="evento" id="evento" class="form-control">
                                    <option value="">Todos los eventos</option>
                                    @foreach(\Spatie\Activitylog\Models\Activity::select('event')->distinct()->pluck('event')->filter() as $evento)
                                        <option value="{{ $evento }}" {{ request('evento') == $evento ? 'selected' : '' }}>
                                            {{ ucfirst($evento) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label for="fecha_desde" class="form-label">Fecha Desde</label>
                                <input type="date" name="fecha_desde" id="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                            </div>
                            
                            <div class="col-md-2">
                                <label for="fecha_hasta" class="form-label">Fecha Hasta</label>
                                <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter"></i> Filtrar
                                    </button>
                                    <a href="{{ route('auditoria.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Limpiar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Tabla de auditorías -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Fecha/Hora</th>
                                    <th>Usuario</th>
                                    <th>Evento</th>
                                    <th>Modelo</th>
                                    <th>Descripción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($auditorias as $auditoria)
                                    <tr>
                                        <td>
                                            <small>{{ $auditoria->created_at->format('d/m/Y H:i:s') }}</small>
                                        </td>
                                        <td>
                                            @if($auditoria->causer)
                                                {{ $auditoria->causer->correo ?? 'N/A' }}
                                            @else
                                                <em>Sistema</em>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $auditoria->event == 'created' ? 'success' : ($auditoria->event == 'updated' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($auditoria->event) }}
                                            </span>
                                        </td>
                                        <td>
                                            <code>{{ class_basename($auditoria->subject_type) }}</code>
                                        </td>
                                        <td>
                                            {{ $auditoria->description ?? 'Sin descripción' }}
                                        </td>
                                        <td>
                                            <a href="{{ route('auditoria.show', $auditoria->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <em>No se encontraron registros de auditoría</em>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-center">
                        {{ $auditorias->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.badge {
    font-size: 0.75em;
}
code {
    background-color: #f8f9fa;
    padding: 2px 4px;
    border-radius: 3px;
    font-size: 0.85em;
}
</style>
@endpush
