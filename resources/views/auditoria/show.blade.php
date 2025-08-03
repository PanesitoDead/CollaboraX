@extends('layouts.app')

@section('title', 'Detalle de Auditoría')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i>
                        Detalle de Auditoría #{{ $auditoria->id }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('auditoria.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Información General</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">ID</th>
                                    <td>{{ $auditoria->id }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha/Hora</th>
                                    <td>{{ $auditoria->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Evento</th>
                                    <td>
                                        <span class="badge badge-{{ $auditoria->event == 'created' ? 'success' : ($auditoria->event == 'updated' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($auditoria->event) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Usuario</th>
                                    <td>
                                        @if($auditoria->causer)
                                            {{ $auditoria->causer->correo ?? 'N/A' }}
                                            <small>(ID: {{ $auditoria->causer_id }})</small>
                                        @else
                                            <em>Sistema</em>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Modelo</th>
                                    <td>
                                        <code>{{ $auditoria->subject_type }}</code>
                                        @if($auditoria->subject_id)
                                            <small>(ID: {{ $auditoria->subject_id }})</small>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Descripción</th>
                                    <td>{{ $auditoria->description ?? 'Sin descripción' }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Objeto Afectado</h5>
                            @if($auditoria->subject)
                                <div class="alert alert-info">
                                    <strong>Estado Actual:</strong>
                                    <pre class="mt-2">{{ json_encode($auditoria->subject->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    El objeto ya no existe en el sistema.
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($auditoria->properties->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>Cambios Realizados</h5>
                                
                                @if($auditoria->event === 'updated' && isset($auditoria->properties['old']) && isset($auditoria->properties['attributes']))
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Valores Anteriores</h6>
                                            <div class="alert alert-warning">
                                                <pre>{{ json_encode($auditoria->properties['old'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Nuevos Valores</h6>
                                            <div class="alert alert-success">
                                                <pre>{{ json_encode($auditoria->properties['attributes'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <h6>Campos Modificados</h6>
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Campo</th>
                                                        <th>Valor Anterior</th>
                                                        <th>Nuevo Valor</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($auditoria->properties['old'] as $field => $oldValue)
                                                        @if(isset($auditoria->properties['attributes'][$field]))
                                                            <tr>
                                                                <td><strong>{{ $field }}</strong></td>
                                                                <td>
                                                                    <span class="text-danger">{{ $oldValue ?? 'NULL' }}</span>
                                                                </td>
                                                                <td>
                                                                    <span class="text-success">{{ $auditoria->properties['attributes'][$field] ?? 'NULL' }}</span>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <strong>Propiedades:</strong>
                                        <pre class="mt-2">{{ json_encode($auditoria->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
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
pre {
    background-color: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
    font-size: 0.85em;
    max-height: 300px;
    overflow-y: auto;
}
</style>
@endpush
