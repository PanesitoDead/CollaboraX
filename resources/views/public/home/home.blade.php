@extends('layouts.app')

@section('content')
<div class="flex min-h-screen flex-col">
    <!-- Header -->
    <header class="sticky top-0 z-50 border-b bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/60">
        <div class="container mx-auto flex h-16 items-center justify-between px-4">
            <div class="flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600">
                    <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <circle cx="12" cy="12" r="6"/>
                        <circle cx="12" cy="12" r="2"/>
                    </svg>
                </div>
                <span class="text-xl font-bold">CollaboraX</span>
            </div>
            <nav class="hidden md:flex items-center gap-6">
                <a href="#caracteristicas" class="text-sm font-medium hover:text-blue-600 transition-colors">
                    Características
                </a>
                <a href="#planes" class="text-sm font-medium hover:text-blue-600 transition-colors">
                    Planes
                </a>
                <a href="{{ route('login') }}" class="text-sm font-medium hover:text-blue-600 transition-colors">
                    Iniciar Sesión
                </a>
                <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors">
                    Comenzar Gratis
                </a>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-blue-50 via-white to-gray-50">
        <div class="absolute inset-0 bg-grid-black/[0.02] bg-[size:60px_60px]"></div>
        <div class="container mx-auto relative py-24 lg:py-32 px-4">
            <div class="mx-auto max-w-4xl text-center">
                <h1 class="text-4xl font-bold tracking-tight sm:text-6xl lg:text-7xl">
                    Transforma la
                    <span class="bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                        productividad
                    </span>
                    de tu equipo
                </h1>

                <p class="mx-auto mt-6 max-w-2xl text-lg text-gray-600 sm:text-xl">
                    La plataforma todo-en-uno que conecta metas, actividades y equipos. Gestiona proyectos con tableros
                    Kanban, comunicación integrada y métricas en tiempo real.
                </p>

                <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('login') }}" class="group bg-blue-600 text-white px-6 py-3 rounded-md font-medium hover:bg-blue-700 transition-colors flex items-center">
                        Comenzar Gratis
                        <svg class="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                    <a href="#demo" class="group border border-gray-300 text-gray-700 px-6 py-3 rounded-md font-medium hover:bg-gray-50 transition-colors flex items-center">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <polygon points="5,3 19,12 5,21"/>
                        </svg>
                        Ver Demo
                    </a>
                </div>

                <div class="mt-12 flex items-center justify-center gap-8 text-sm text-gray-500">
                    <div class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <polyline points="20,6 9,17 4,12"/>
                        </svg>
                        Prueba gratuita 14 días
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <polyline points="20,6 9,17 4,12"/>
                        </svg>
                        Sin tarjeta de crédito
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <polyline points="20,6 9,17 4,12"/>
                        </svg>
                        Configuración en 5 minutos
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="border-b bg-gray-50 py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 gap-8 md:grid-cols-4">
                @php
                $stats = [
                    ['number' => '10,000+', 'label' => 'Empresas activas'],
                    ['number' => '500K+', 'label' => 'Usuarios registrados'],
                    ['number' => '99.9%', 'label' => 'Tiempo de actividad'],
                    ['number' => '4.9/5', 'label' => 'Calificación promedio']
                ];
                @endphp
                @foreach($stats as $stat)
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600 md:text-3xl">{{ $stat['number'] }}</div>
                    <div class="text-sm text-gray-500">{{ $stat['label'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="caracteristicas" class="py-24">
        <div class="container mx-auto px-4">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                    Todo lo que necesitas para gestionar tu equipo
                </h2>
                <p class="mt-4 text-lg text-gray-600">
                    Herramientas poderosas diseñadas para equipos modernos que buscan resultados excepcionales
                </p>
            </div>

            <div class="mt-16 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @php
                $features = [
                    [
                        'icon' => '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>',
                        'title' => 'Gestión de Metas Inteligente',
                        'description' => 'Establece objetivos claros, asigna responsables y realiza seguimiento automático del progreso con métricas en tiempo real.',
                        'color' => 'text-blue-500',
                        'bgColor' => 'bg-blue-50'
                    ],
                    [
                        'icon' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="m22 21-3-3m0 0a5.5 5.5 0 1 0-7.78-7.78 5.5 5.5 0 0 0 7.78 7.78Z"/>',
                        'title' => 'Colaboración Sin Límites',
                        'description' => 'Organiza equipos por áreas, asigna roles específicos y mantén a todos alineados con herramientas de comunicación integradas.',
                        'color' => 'text-green-500',
                        'bgColor' => 'bg-green-50'
                    ],
                    [
                        'icon' => '<rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/>',
                        'title' => 'Reuniones Productivas',
                        'description' => 'Programa reuniones con agendas claras, graba sesiones importantes y da seguimiento automático a los acuerdos.',
                        'color' => 'text-purple-500',
                        'bgColor' => 'bg-purple-50'
                    ],
                    [
                        'icon' => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
                        'title' => 'Comunicación Centralizada',
                        'description' => 'Chat directo y grupal, notificaciones inteligentes y un historial completo de todas las conversaciones del proyecto.',
                        'color' => 'text-orange-500',
                        'bgColor' => 'bg-orange-50'
                    ],
                    [
                        'icon' => '<path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/>',
                        'title' => 'Analytics Avanzados',
                        'description' => 'Dashboards personalizables, reportes automáticos y métricas de rendimiento que impulsan la toma de decisiones.',
                        'color' => 'text-red-500',
                        'bgColor' => 'bg-red-50'
                    ],
                    [
                        'icon' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/>',
                        'title' => 'Seguridad Empresarial',
                        'description' => 'Autenticación robusta, permisos granulares y cumplimiento con estándares internacionales de seguridad.',
                        'color' => 'text-indigo-500',
                        'bgColor' => 'bg-indigo-50'
                    ]
                ];
                @endphp
                @foreach($features as $feature)
                <div class="group relative overflow-hidden border border-gray-200 rounded-lg bg-white p-6 transition-all hover:shadow-lg">
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-lg {{ $feature['bgColor'] }} mb-4">
                        <svg class="h-6 w-6 {{ $feature['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $feature['icon'] !!}
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">{{ $feature['title'] }}</h3>
                    <p class="text-gray-600">{{ $feature['description'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- How it Works -->
    <section class="bg-gray-50 py-24">
        <div class="container mx-auto px-4">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Cómo funciona CollaboraX</h2>
                <p class="mt-4 text-lg text-gray-600">
                    Tres pasos simples para transformar la productividad de tu equipo
                </p>
            </div>

            <div class="mt-16 grid gap-8 md:grid-cols-3">
                @php
                $steps = [
                    [
                        'step' => '01',
                        'title' => 'Configura tu Organización',
                        'description' => 'Crea áreas, equipos y asigna roles. Invita a tu equipo y define la estructura organizacional en minutos.'
                    ],
                    [
                        'step' => '02',
                        'title' => 'Define Metas y Actividades',
                        'description' => 'Establece objetivos claros, crea actividades específicas y asigna responsables con fechas límite.'
                    ],
                    [
                        'step' => '03',
                        'title' => 'Monitorea y Optimiza',
                        'description' => 'Usa tableros Kanban para seguimiento visual, analiza métricas y ajusta estrategias en tiempo real.'
                    ]
                ];
                @endphp
                @foreach($steps as $index => $step)
                <div class="relative">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-600 text-white font-bold">
                            {{ $step['step'] }}
                        </div>
                        @if($index < 2)
                        <div class="hidden md:block flex-1 h-px bg-gray-300"></div>
                        @endif
                    </div>
                    <h3 class="text-xl font-semibold mb-3">{{ $step['title'] }}</h3>
                    <p class="text-gray-600">{{ $step['description'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section id="planes" class="py-24">
        <div class="container mx-auto px-4">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Planes diseñados para crecer contigo</h2>
                <p class="mt-4 text-lg text-gray-600">
                    Desde startups hasta empresas globales, tenemos el plan perfecto para tu organización
                </p>
            </div>

            <div class="mt-16 grid gap-8 lg:grid-cols-3">
                @php
                $plans = [
                    [
                        'name' => 'Standard',
                        'badge' => 'Más Popular',
                        'badgeClass' => 'bg-gray-100 text-gray-800',
                        'description' => 'Perfecto para equipos pequeños y medianos',
                        'price' => 'S/ 99.90',
                        'period' => '/mes',
                        'features' => [
                            'Hasta 25 usuarios',
                            'Tableros Kanban ilimitados',
                            'Gestión de metas y actividades',
                            'Chat y reuniones básicas',
                            'Reportes mensuales',
                            'Soporte por email',
                            'Almacenamiento 10GB'
                        ],
                        'buttonText' => 'Comenzar Prueba Gratuita',
                        'buttonClass' => 'bg-blue-600 text-white hover:bg-blue-700',
                        'cardClass' => 'border-gray-200'
                    ],
                    [
                        'name' => 'Business',
                        'badge' => 'Recomendado',
                        'badgeClass' => 'bg-blue-600 text-white',
                        'description' => 'Ideal para empresas en crecimiento',
                        'price' => 'S/ 249.90',
                        'period' => '/mes',
                        'features' => [
                            'Hasta 100 usuarios',
                            'Todo lo incluido en Standard',
                            'Analytics avanzados y BI',
                            'Integraciones con terceros',
                            'Roles y permisos personalizados',
                            'Automatizaciones',
                            'Soporte prioritario 24/7',
                            'Almacenamiento 100GB',
                            'API completa'
                        ],
                        'buttonText' => 'Comenzar Prueba Gratuita',
                        'buttonClass' => 'bg-blue-600 text-white hover:bg-blue-700',
                        'cardClass' => 'border-2 border-blue-600 shadow-lg scale-105'
                    ],
                    [
                        'name' => 'Enterprise',
                        'badge' => 'Personalizable',
                        'badgeClass' => 'bg-gray-100 text-gray-800 border border-gray-300',
                        'description' => 'Para organizaciones grandes y complejas',
                        'price' => 'S/ 599.90',
                        'period' => '/mes',
                        'features' => [
                            'Usuarios ilimitados',
                            'Todo lo incluido en Business',
                            'Servidor dedicado',
                            'Personalización completa',
                            'SSO y seguridad avanzada',
                            'Implementación guiada',
                            'Gerente de cuenta dedicado',
                            'Almacenamiento ilimitado',
                            'SLA garantizado 99.9%'
                        ],
                        'buttonText' => 'Contactar Ventas',
                        'buttonClass' => 'border border-gray-300 text-gray-700 hover:bg-gray-50',
                        'cardClass' => 'border-gray-200'
                    ]
                ];
                @endphp
                @foreach($plans as $plan)
                <div class="relative overflow-hidden border rounded-lg bg-white {{ $plan['cardClass'] }}">
                    @if($plan['name'] === 'Business')
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-600 to-blue-800"></div>
                    @endif
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-xl font-semibold">{{ $plan['name'] }}</h3>
                            <span class="px-2 py-1 text-xs font-medium rounded {{ $plan['badgeClass'] }}">
                                {{ $plan['badge'] }}
                            </span>
                        </div>
                        <p class="text-gray-600 mb-4">{{ $plan['description'] }}</p>
                        <div class="mb-6">
                            <span class="text-4xl font-bold">{{ $plan['price'] }}</span>
                            <span class="text-gray-500">{{ $plan['period'] }}</span>
                        </div>
                        <ul class="space-y-3 mb-6">
                            @foreach($plan['features'] as $feature)
                            <li class="flex items-center gap-3">
                                <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <polyline points="20,6 9,17 4,12"/>
                                </svg>
                                <span class="text-sm">{{ $feature }}</span>
                            </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('register') }}?plan={{ strtolower($plan['name']) }}" 
                           class="w-full group px-4 py-2 rounded-md font-medium transition-colors flex items-center justify-center {{ $plan['buttonClass'] }}">
                            {{ $plan['buttonText'] }}
                            <svg class="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-12 text-center">
                <p class="text-sm text-gray-500">
                    Todos los planes incluyen prueba gratuita de 14 días. Sin compromisos, cancela cuando quieras.
                </p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-gray-50 py-24">
        <div class="container mx-auto px-4">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">¿Listo para transformar tu equipo?</h2>
                <p class="mt-4 text-lg text-gray-600">
                    Únete a miles de empresas que ya están mejorando su productividad con CollaboraX
                </p>
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('register') }}" class="group bg-blue-600 text-white px-6 py-3 rounded-md font-medium hover:bg-blue-700 transition-colors flex items-center">
                        Comenzar Gratis Ahora
                        <svg class="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                    <a href="{{ route('login') }}" class="border border-gray-300 text-gray-700 px-6 py-3 rounded-md font-medium hover:bg-gray-50 transition-colors">
                        Ya tengo cuenta
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t bg-white">
        <div class="container mx-auto py-12 px-4">
            <div class="grid gap-8 md:grid-cols-4">
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600">
                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <circle cx="12" cy="12" r="6"/>
                                <circle cx="12" cy="12" r="2"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold">CollaboraX</span>
                    </div>
                    <p class="text-sm text-gray-500">
                        La plataforma de productividad que transforma equipos y acelera resultados.
                    </p>
                </div>

                <div class="space-y-4">
                    <h4 class="font-semibold">Producto</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="#caracteristicas" class="hover:text-gray-900 transition-colors">Características</a></li>
                        <li><a href="#planes" class="hover:text-gray-900 transition-colors">Precios</a></li>
                        <li><a href="#" class="hover:text-gray-900 transition-colors">Integraciones</a></li>
                        <li><a href="#" class="hover:text-gray-900 transition-colors">API</a></li>
                    </ul>
                </div>

                <div class="space-y-4">
                    <h4 class="font-semibold">Empresa</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-gray-900 transition-colors">Acerca de</a></li>
                        <li><a href="#" class="hover:text-gray-900 transition-colors">Blog</a></li>
                        <li><a href="#" class="hover:text-gray-900 transition-colors">Carreras</a></li>
                        <li><a href="#" class="hover:text-gray-900 transition-colors">Contacto</a></li>
                    </ul>
                </div>

                <div class="space-y-4">
                    <h4 class="font-semibold">Soporte</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-gray-900 transition-colors">Centro de Ayuda</a></li>
                        <li><a href="#" class="hover:text-gray-900 transition-colors">Documentación</a></li>
                        <li><a href="#" class="hover:text-gray-900 transition-colors">Estado del Sistema</a></li>
                        <li><a href="#" class="hover:text-gray-900 transition-colors">Seguridad</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t pt-8 md:flex-row">
                <p class="text-center text-sm text-gray-500 md:text-left">
                    &copy; {{ date('Y') }} CollaboraX. Todos los derechos reservados.
                </p>
                <div class="flex gap-6">
                    <a href="#" class="text-sm text-gray-500 hover:text-gray-900 transition-colors">Términos de Servicio</a>
                    <a href="#" class="text-sm text-gray-500 hover:text-gray-900 transition-colors">Política de Privacidad</a>
                    <a href="#" class="text-sm text-gray-500 hover:text-gray-900 transition-colors">Cookies</a>
                </div>
            </div>
        </div>
    </footer>
</div>
@endsection