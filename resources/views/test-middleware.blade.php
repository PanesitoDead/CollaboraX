@extends('layouts.app')

@section('title', 'Página de Prueba de Middleware')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-shield-alt"></i> Prueba de Middleware - Acceso por Roles</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <h5><i class="fas fa-check-circle"></i> ¡Acceso Autorizado!</h5>
                        <p>Si puedes ver esta página, significa que el middleware está funcionando correctamente.</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6><i class="fas fa-user"></i> Información del Usuario</h6>
                                    <ul class="list-unstyled">
                                        <li><strong>Email:</strong> {{ auth()->user()->correo ?? 'No disponible' }}</li>
                                        <li><strong>ID:</strong> {{ auth()->user()->id ?? 'No disponible' }}</li>
                                        <li><strong>Estado:</strong> 
                                            @if(auth()->user()->activo ?? false)
                                                <span class="badge badge-success">Activo</span>
                                            @else
                                                <span class="badge badge-danger">Inactivo</span>
                                            @endif
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6><i class="fas fa-user-tag"></i> Información del Rol</h6>
                                    <p><strong>Rol Actual:</strong> 
                                        @if(isset($userRole))
                                            <span class="badge badge-primary">{{ $userRole }}</span>
                                        @else
                                            <span class="badge badge-warning">No determinado</span>
                                        @endif
                                    </p>
                                    <p><strong>Rol Requerido:</strong> 
                                        <span class="badge badge-info">{{ $requiredRole ?? 'Variable' }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h6><i class="fas fa-cogs"></i> Rutas de Prueba por Rol</h6>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="list-group">
                                    <a href="/test-middleware/super-admin" class="list-group-item list-group-item-action">
                                        <i class="fas fa-crown text-warning"></i> Super Admin - Solo Super Administradores
                                    </a>
                                    <a href="/test-middleware/admin" class="list-group-item list-group-item-action">
                                        <i class="fas fa-users-cog text-primary"></i> Admin - Solo Administradores
                                    </a>
                                    <a href="/test-middleware/coord-general" class="list-group-item list-group-item-action">
                                        <i class="fas fa-user-tie text-success"></i> Coord. General - Solo Coordinadores Generales
                                    </a>
                                    <a href="/test-middleware/coord-equipo" class="list-group-item list-group-item-action">
                                        <i class="fas fa-users text-info"></i> Coord. Equipo - Solo Coordinadores de Equipo
                                    </a>
                                    <a href="/test-middleware/colaborador" class="list-group-item list-group-item-action">
                                        <i class="fas fa-user text-secondary"></i> Colaborador - Solo Colaboradores
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Instrucciones de Prueba</h6>
                            <ol>
                                <li>Haz clic en las rutas de prueba arriba</li>
                                <li>Si tu rol coincide, verás una página de éxito</li>
                                <li>Si tu rol no coincide, verás la página de acceso denegado</li>
                                <li>Esto confirma que el middleware está funcionando correctamente</li>
                            </ol>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <a href="/dashboard" class="btn btn-primary">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
