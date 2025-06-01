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
                            @csrf
                            <div class="flex items-start space-x-6">
                                <!-- Avatar -->
                                <div class="flex flex-col items-center">
                                    <div id="avatar-container" class="w-24 h-24 bg-gray-300 rounded-full flex items-center justify-center mb-3 form-transition hover-scale overflow-hidden">
                                        @if($userData['foto_url'])
                                            <img id="avatar-image" src="{{ $userData['foto_url'] }}" alt="Foto de perfil" class="w-full h-full object-cover">
                                        @else
                                            <div id="avatar-initials" class="text-2xl font-bold text-gray-600">{{ $userData['iniciales'] }}</div>
                                        @endif
                                    </div>
                                    <input type="file" id="photo-input" accept="image/*" class="hidden">
                                    <button type="button" onclick="document.getElementById('photo-input').click()" class="text-sm text-blue-600 hover:text-blue-700 font-medium tab-transition">
                                        Cambiar Foto
                                    </button>
                                </div>
                                
                                <!-- Form Fields -->
                                <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombres</label>
                                        <input type="text" name="nombres" value="{{ $userData['nombres'] }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Apellido Paterno</label>
                                        <input type="text" name="apellido_paterno" value="{{ $userData['apellido_paterno'] }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Apellido Materno</label>
                                        <input type="text" name="apellido_materno" value="{{ $userData['apellido_materno'] }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Correo Electrónico</label>
                                        <input type="email" name="correo" value="{{ $userData['correo'] }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                                        <input type="tel" name="telefono" value="{{ $userData['telefono'] }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Documento de Identidad</label>
                                        <input type="text" name="doc_identidad" value="{{ $userData['doc_identidad'] }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Nacimiento</label>
                                        <input type="date" name="fecha_nacimiento" value="{{ $userData['fecha_nacimiento'] }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
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

        <!-- Seguridad Tab -->
        <div id="content-seguridad" class="tab-content hidden">
            <div class="w-full space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 form-transition hover-scale relative pb-24">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Cambiar Contraseña</h3>
                        <p class="text-gray-600 mb-6">Actualiza tu contraseña para mantener tu cuenta segura</p>
                        
                        <form id="security-form" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Contraseña Actual</label>
                                <input type="password" name="current_password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nueva Contraseña</label>
                                <input type="password" name="new_password" id="new_password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
    
    <!-- Indicadores de validación en tiempo real -->
    <div id="password-requirements" class="mt-2 space-y-1">
        <div id="length-check" class="flex items-center text-xs">
            <span class="w-3 h-3 mr-1 text-red-500">✗</span>
            <span class="text-red-500">Mínimo 8 caracteres</span>
        </div>
        <div id="uppercase-check" class="flex items-center text-xs">
            <span class="w-3 h-3 mr-1 text-red-500">✗</span>
            <span class="text-red-500">Al menos una letra mayúscula</span>
        </div>
        <div id="lowercase-check" class="flex items-center text-xs">
            <span class="w-3 h-3 mr-1 text-red-500">✗</span>
            <span class="text-red-500">Al menos una letra minúscula</span>
        </div>
        <div id="number-check" class="flex items-center text-xs">
            <span class="w-3 h-3 mr-1 text-red-500">✗</span>
            <span class="text-red-500">Al menos un número</span>
        </div>
        <div id="different-check" class="flex items-center text-xs">
            <span class="w-3 h-3 mr-1 text-gray-400">-</span>
            <span class="text-gray-400">Debe ser diferente a la contraseña actual</span>
        </div>
    </div>
</div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Confirmar Nueva Contraseña</label>
                                <input type="password" name="new_password_confirmation" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent form-transition">
                            </div>
                        </form>
                    </div>
                    
                    <!-- Button inside card -->
                    <div class="absolute bottom-6 right-6">
                        <button onclick="saveSecurity()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium shadow-lg hover:shadow-xl tab-transition hover-scale">
                            <i data-lucide="save" class="w-4 h-4 inline mr-2"></i>
                            Actualizar Contraseña
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

<!-- Error Toast -->
<div id="error-toast" class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full opacity-0 transition-all duration-300 z-50">
    <div class="flex items-center">
        <i data-lucide="alert-circle" class="w-5 h-5 mr-2"></i>
        <span id="error-toast-message">Error al procesar la solicitud</span>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
        <span class="text-gray-700">Procesando...</span>
    </div>
</div>

<script>
    // Tab functionality
    function showTab(tabName) {
        // Si estamos saliendo de la pestaña de seguridad, limpiar los campos de contraseña
        const currentActiveTab = document.querySelector('.tab-button.border-blue-500');
        if (currentActiveTab && currentActiveTab.id === 'tab-seguridad' && tabName !== 'seguridad') {
            clearSecurityForm();
        }
        
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

    // Save profile function
    function saveProfile() {
        const form = document.getElementById('profile-form');
        const formData = new FormData(form);
        
        showLoading();
        
        fetch('{{ route("coordinador-general.configuracion.update-profile") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showToast(data.message);
                // Actualizar iniciales si cambió el nombre
                if (data.data && data.data.iniciales) {
                    const avatarInitials = document.getElementById('avatar-initials');
                    if (avatarInitials) {
                        avatarInitials.textContent = data.data.iniciales;
                    }
                }
            } else {
                showErrorToast(data.error || 'Error al actualizar el perfil');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            showErrorToast('Error al actualizar el perfil');
        });
    }

    // Upload photo function
    function uploadPhoto(file) {
    const formData = new FormData();
    formData.append('photo', file);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    showLoading();
    
    fetch('{{ route("coordinador-general.configuracion.upload-photo") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // Verificar si la respuesta es exitosa
        if (!response.ok) {
            // Si no es exitosa, intentar obtener el mensaje de error
            return response.json().then(errorData => {
                throw new Error(errorData.error || `Error del servidor: ${response.status}`);
            }).catch(() => {
                throw new Error(`Error del servidor: ${response.status} - ${response.statusText}`);
            });
        }
        return response.json();
    })
    .then(data => {
        hideLoading();
        if (data.success) {
            showToast(data.message);
            // Actualizar la imagen del avatar
            const avatarContainer = document.getElementById('avatar-container');
            if (data.photo_url) {
                avatarContainer.innerHTML = `<img id="avatar-image" src="${data.photo_url}?v=${new Date().getTime()}" alt="Foto de perfil" class="w-full h-full object-cover">`;
            } else {
                showErrorToast('No se pudo actualizar la imagen del avatar');
            }
        } else {
            showErrorToast(data.error || 'Error al subir la foto');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error completo:', error);
        showErrorToast(error.message || 'Error al subir la foto');
    });
}

    // Save security function
    // Validación en tiempo real de contraseña
document.getElementById('new_password').addEventListener('input', function(e) {
    const password = e.target.value;
    
    // Verificar longitud mínima
    const lengthCheck = document.getElementById('length-check');
    if (password.length >= 8) {
        lengthCheck.innerHTML = '<span class="w-3 h-3 mr-1 text-green-500">✓</span><span class="text-green-500">Mínimo 8 caracteres</span>';
    } else {
        lengthCheck.innerHTML = '<span class="w-3 h-3 mr-1 text-red-500">✗</span><span class="text-red-500">Mínimo 8 caracteres</span>';
    }
    
    // Verificar mayúscula
    const uppercaseCheck = document.getElementById('uppercase-check');
    if (/[A-Z]/.test(password)) {
        uppercaseCheck.innerHTML = '<span class="w-3 h-3 mr-1 text-green-500">✓</span><span class="text-green-500">Al menos una letra mayúscula</span>';
    } else {
        uppercaseCheck.innerHTML = '<span class="w-3 h-3 mr-1 text-red-500">✗</span><span class="text-red-500">Al menos una letra mayúscula</span>';
    }
    
    // Verificar minúscula
    const lowercaseCheck = document.getElementById('lowercase-check');
    if (/[a-z]/.test(password)) {
        lowercaseCheck.innerHTML = '<span class="w-3 h-3 mr-1 text-green-500">✓</span><span class="text-green-500">Al menos una letra minúscula</span>';
    } else {
        lowercaseCheck.innerHTML = '<span class="w-3 h-3 mr-1 text-red-500">✗</span><span class="text-red-500">Al menos una letra minúscula</span>';
    }
    
    // Verificar número
    const numberCheck = document.getElementById('number-check');
    if (/[0-9]/.test(password)) {
        numberCheck.innerHTML = '<span class="w-3 h-3 mr-1 text-green-500">✓</span><span class="text-green-500">Al menos un número</span>';
    } else {
        numberCheck.innerHTML = '<span class="w-3 h-3 mr-1 text-red-500">✗</span><span class="text-red-500">Al menos un número</span>';
    }

    // Verificar que sea diferente a la contraseña actual
    const currentPassword = document.querySelector('[name="current_password"]').value;
    const differentCheck = document.getElementById('different-check');
    if (currentPassword && password && password === currentPassword) {
        differentCheck.innerHTML = '<span class="w-3 h-3 mr-1 text-red-500">✗</span><span class="text-red-500">Debe ser diferente a la contraseña actual</span>';
    } else if (currentPassword && password && password !== currentPassword) {
        differentCheck.innerHTML = '<span class="w-3 h-3 mr-1 text-green-500">✓</span><span class="text-green-500">Debe ser diferente a la contraseña actual</span>';
    } else {
        differentCheck.innerHTML = '<span class="w-3 h-3 mr-1 text-gray-400">-</span><span class="text-gray-400">Debe ser diferente a la contraseña actual</span>';
    }
});

// Validar antes de enviar el formulario
function saveSecurity() {
    const form = document.getElementById('security-form');
    const formData = new FormData(form);
    
    // Validar que las contraseñas coincidan
    const newPassword = form.querySelector('[name="new_password"]').value;
    const confirmPassword = form.querySelector('[name="new_password_confirmation"]').value;
    
    if (!newPassword || !confirmPassword) {
        showErrorToast('Por favor, complete ambos campos de contraseña.');
        return;
    }

    if (newPassword !== confirmPassword) {
        showErrorToast('Las contraseñas no coinciden');
        return;
    }
    
    // Validar requisitos de contraseña
    if (newPassword.length < 8) {
        showErrorToast('La contraseña debe tener al menos 8 caracteres');
        return;
    }
    
    if (!/[A-Z]/.test(newPassword)) {
        showErrorToast('La contraseña debe tener al menos una letra mayúscula');
        return;
    }
    
    if (!/[a-z]/.test(newPassword)) {
        showErrorToast('La contraseña debe tener al menos una letra minúscula');
        return;
    }
    
    if (!/[0-9]/.test(newPassword)) {
        showErrorToast('La contraseña debe tener al menos un número');
        return;
    }

    // Validar que la nueva contraseña no sea igual a la actual
    const currentPassword = form.querySelector('[name="current_password"]').value;
    if (newPassword === currentPassword) {
        showErrorToast('La nueva contraseña debe ser diferente a la contraseña actual');
        return;
    }
    
    showLoading();
    
    fetch('{{ route("coordinador-general.configuracion.update-security") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showToast(data.message);
            // Limpiar el formulario
            form.reset();
            // Resetear indicadores de validación
            document.getElementById('new_password').dispatchEvent(new Event('input'));
        } else {
            showErrorToast(data.error || 'Error al actualizar la contraseña');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showErrorToast('Error al actualizar la contraseña');
    });
}

// Función adicional para limpiar formulario cuando se hace clic en otra pestaña
function clearSecurityForm() {
    const securityForm = document.getElementById('security-form');
    if (securityForm) {
        // Limpiar todos los campos del formulario
        securityForm.reset();
        
        // Resetear todos los indicadores de validación a su estado inicial
        document.getElementById('length-check').innerHTML = '<span class="w-3 h-3 mr-1 text-red-500">✗</span><span class="text-red-500">Mínimo 8 caracteres</span>';
        document.getElementById('uppercase-check').innerHTML = '<span class="w-3 h-3 mr-1 text-red-500">✗</span><span class="text-red-500">Al menos una letra mayúscula</span>';
        document.getElementById('lowercase-check').innerHTML = '<span class="w-3 h-3 mr-1 text-red-500">✗</span><span class="text-red-500">Al menos una letra minúscula</span>';
        document.getElementById('number-check').innerHTML = '<span class="w-3 h-3 mr-1 text-red-500">✗</span><span class="text-red-500">Al menos un número</span>';
        document.getElementById('different-check').innerHTML = '<span class="w-3 h-3 mr-1 text-gray-400">-</span><span class="text-gray-400">Debe ser diferente a la contraseña actual</span>';
    }
}

    // Toast notifications
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

    function showErrorToast(message) {
        const toast = document.getElementById('error-toast');
        const toastMessage = document.getElementById('error-toast-message');
        
        toastMessage.textContent = message;
        toast.classList.remove('translate-x-full', 'opacity-0');
        toast.classList.add('translate-x-0', 'opacity-100');
        
        setTimeout(() => {
            toast.classList.remove('translate-x-0', 'opacity-100');
            toast.classList.add('translate-x-full', 'opacity-0');
        }, 4000);
    }

    // Loading overlay
    function showLoading() {
        document.getElementById('loading-overlay').classList.remove('hidden');
        document.getElementById('loading-overlay').classList.add('flex');
    }

    function hideLoading() {
        document.getElementById('loading-overlay').classList.add('hidden');
        document.getElementById('loading-overlay').classList.remove('flex');
    }

    // Photo upload handler
    document.getElementById('photo-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Validar tipo de archivo
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            showErrorToast('Por favor selecciona una imagen válida (JPG, PNG, GIF o WebP)');
            this.value = ''; // Limpiar el input
            return;
        }
        
        // Validar tamaño (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            showErrorToast('La imagen debe ser menor a 5MB');
            this.value = ''; // Limpiar el input
            return;
        }
        
        // Validar dimensiones mínimas
        const img = new Image();
        img.onload = function() {
            if (this.width < 100 || this.height < 100) {
                showErrorToast('La imagen debe tener al menos 100x100 píxeles');
                document.getElementById('photo-input').value = '';
                return;
            }
            uploadPhoto(file);
        };
        img.onerror = function() {
            showErrorToast('No se pudo cargar la imagen seleccionada');
            document.getElementById('photo-input').value = '';
        };
        img.src = URL.createObjectURL(file);
    }
});

    // Initialize icons when page loads
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });

    // Validación cuando cambia la contraseña actual
    document.querySelector('[name="current_password"]').addEventListener('input', function() {
        // Trigger validation of new password if it has content
        const newPasswordField = document.getElementById('new_password');
        if (newPasswordField.value) {
            newPasswordField.dispatchEvent(new Event('input'));
        }
    });
</script>

<style>
.sidebar-transition {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.tab-transition {
    transition: all 0.2s ease-in-out;
}
.form-transition {
    transition: all 0.3s ease-in-out;
}
.hover-scale {
    transition: transform 0.2s ease-in-out;
}
.hover-scale:hover {
    transform: scale(1.02);
}
.slide-in {
    animation: slideIn 0.3s ease-out;
}
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endsection
