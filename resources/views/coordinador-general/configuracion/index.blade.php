{{-- resources/views/coordinador-general/configuracion/index.blade.php --}}
@extends('layouts.coordinador-general.app')

@section('content')
<div class="flex-1 overflow-auto">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 px-6 py-4 slide-in">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Configuración</h1>
                <p class="text-gray-600 mt-1">Gestiona tu cuenta y preferencias</p>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white border-b border-gray-200 px-6 slide-in">
        <nav class="flex space-x-8">
            <button onclick="showTab('perfil')" id="tab-perfil" class="tab-button py-4 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600 tab-transition">
                <i data-lucide="user" class="w-4 h-4 inline mr-2"></i>
                Perfil
            </button>
            <button onclick="showTab('notificaciones')" id="tab-notificaciones" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 tab-transition">
                <i data-lucide="bell" class="w-4 h-4 inline mr-2"></i>
                Notificaciones
            </button>
            <button onclick="showTab('seguridad')" id="tab-seguridad" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 tab-transition">
                <i data-lucide="shield" class="w-4 h-4 inline mr-2"></i>
                Seguridad
            </button>
        </nav>
    </div>

    <!-- Content -->
    <div class="flex-1 p-6">
        <!-- Perfil Tab -->
        <div id="content-perfil" class="tab-content slide-in">
            <div class="w-full">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 form-transition hover-scale relative pb-24">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Información Personal</h3>
                        <p class="text-gray-600 mb-6">Actualiza tu información personal y de contacto</p>
                        
                        <form id="profile-form" class="space-y-6">
                            <div class="flex items-start space-x-6">
                                <!-- Avatar -->
                                <div class="flex flex-col items-center">
                                    <div class="w-24 h-24 bg-gray-300 rounded-full flex items-center justify-center mb-3 form-transition hover-scale">
                                        <i data-lucide="user" class="w-8 h-8 text-gray-500"></i>
                                    </div>
                                    <button type="button" class="text-sm text-blue-600 hover:text-blue-700 font-medium tab-transition">
                                        Cambiar Foto
                                    </button>
                                </div>
                                
                                <!-- Form Fields -->
                                <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                                        <input type="text" value="Miguel" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Apellido</label>
                                        <input type="text" value="González" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Correo Electrónico</label>
                                        <input type="email" value="miguel@empresa.com" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                                        <input type="tel" value="+34 612 345 678" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Button inside card -->
                    <div class="absolute bottom-6 right-6">
                        <button onclick="saveProfile()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium shadow-lg hover:shadow-xl tab-transition hover-scale">
                            <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                            Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notificaciones Tab -->
        <div id="content-notificaciones" class="tab-content hidden">
            <div class="w-full space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 form-transition hover-scale relative pb-24">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Preferencias de Notificaciones</h3>
                        <p class="text-gray-600 mb-6">Configura cómo y cuándo quieres recibir notificaciones</p>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900">Notificaciones por Email</h4>
                                    <p class="text-sm text-gray-600">Recibe actualizaciones importantes por correo</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 tab-transition"></div>
                                </label>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900">Notificaciones Push</h4>
                                    <p class="text-sm text-gray-600">Recibe notificaciones en tiempo real</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 tab-transition"></div>
                                </label>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900">Reuniones</h4>
                                    <p class="text-sm text-gray-600">Recordatorios de reuniones programadas</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 tab-transition"></div>
                                </label>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900">Actividades</h4>
                                    <p class="text-sm text-gray-600">Actualizaciones sobre actividades asignadas</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 tab-transition"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Button inside card -->
                    <div class="absolute bottom-6 right-6">
                        <button onclick="saveNotifications()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium shadow-lg hover:shadow-xl tab-transition hover-scale">
                            <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                            Guardar Preferencias
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seguridad Tab -->
        <div id="content-seguridad" class="tab-content hidden">
            <div class="w-full space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 form-transition hover-scale relative pb-24">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Cambiar Contraseña</h3>
                        <p class="text-gray-600 mb-6">Actualiza tu contraseña para mantener tu cuenta segura</p>
                        
                        <form class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Contraseña Actual</label>
                                <input type="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nueva Contraseña</label>
                                <input type="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Confirmar Nueva Contraseña</label>
                                <input type="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                            </div>
                        </form>
                        
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Autenticación de Dos Factores</h3>
                            <p class="text-gray-600 mb-6">Añade una capa extra de seguridad a tu cuenta</p>
                            
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900">Activar 2FA</h4>
                                    <p class="text-sm text-gray-600">Requiere un código adicional al iniciar sesión</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 tab-transition"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Button inside card -->
                    <div class="absolute bottom-6 right-6">
                        <button onclick="saveSecurity()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium shadow-lg hover:shadow-xl tab-transition hover-scale">
                            <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                            Actualizar Seguridad
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full opacity-0 transition-all duration-300 z-50">
    <div class="flex items-center">
        <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
        <span id="toast-message">Cambios guardados correctamente</span>
    </div>
</div>

<script>
    // Tab functionality
    function showTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Remove active state from all tabs
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('border-blue-500', 'text-blue-600');
            button.classList.add('border-transparent', 'text-gray-500');
        });
        
        // Show selected tab content with animation
        const selectedContent = document.getElementById(`content-${tabName}`);
        selectedContent.classList.remove('hidden');
        selectedContent.classList.add('slide-in');
        
        // Add active state to selected tab
        const selectedTab = document.getElementById(`tab-${tabName}`);
        selectedTab.classList.remove('border-transparent', 'text-gray-500');
        selectedTab.classList.add('border-blue-500', 'text-blue-600');
    }

    // Save functions for each section
    function saveProfile() {
        showToast('Perfil actualizado correctamente');
    }

    function saveNotifications() {
        showToast('Preferencias de notificaciones guardadas');
    }

    function saveSecurity() {
        showToast('Configuración de seguridad actualizada');
    }

    // Toast notification
    function showToast(message) {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toast-message');
        
        toastMessage.textContent = message;
        toast.classList.remove('translate-x-full', 'opacity-0');
        toast.classList.add('translate-x-0', 'opacity-100');
        
        setTimeout(() => {
            toast.classList.remove('translate-x-0', 'opacity-100');
            toast.classList.add('translate-x-full', 'opacity-0');
        }, 3000);
    }

    // Initialize icons when page loads
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endsection