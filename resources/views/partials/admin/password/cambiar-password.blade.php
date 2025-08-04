{{-- Formulario para cambiar contraseña --}}
<form method="POST" action="{{ route('admin.configuracion.update-password') }}" id="password-form">
    @csrf
    @method('PUT')
    
    <div class="space-y-6">
        {{-- Mostrar errores de validación --}}
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Hay errores en el formulario:
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Header de la sección --}}
        <div class="border-b border-gray-200 pb-4">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i data-lucide="lock" class="w-5 h-5 mr-2 text-red-600"></i>
                Cambiar Contraseña
            </h3>
            <p class="mt-1 text-sm text-gray-600">
                Actualice su contraseña para mantener su cuenta segura
            </p>
        </div>

        {{-- Formulario de contraseña --}}
        <div class="bg-gray-50 rounded-lg p-4 space-y-4">
            <div>
                <label for="current_password" class="block mb-1 text-sm font-medium text-gray-700">
                    Contraseña Actual *
                </label>
                <div class="relative">
                    <input
                        type="password"
                        name="current_password"
                        id="current_password"
                        required
                        placeholder="Ingrese su contraseña actual"
                        class="w-full mt-1 px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                    />
                    <button
                        type="button"
                        onclick="togglePasswordVisibility('current_password')"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                    >
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                </div>
                @error('current_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label for="new_password" class="block mb-1 text-sm font-medium text-gray-700">
                        Nueva Contraseña *
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            name="new_password"
                            id="new_password"
                            required
                            placeholder="Mínimo 8 caracteres"
                            class="w-full mt-1 px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                            onkeyup="checkPasswordStrength(this.value)"
                        />
                        <button
                            type="button"
                            onclick="togglePasswordVisibility('new_password')"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                        >
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                    @error('new_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    
                    {{-- Indicador de fortaleza de contraseña --}}
                    <div id="password-strength" class="mt-2 hidden">
                        <div class="flex items-center space-x-1">
                            <div class="flex-1 h-1 rounded-full bg-gray-200">
                                <div id="strength-bar" class="h-1 rounded-full transition-all duration-300"></div>
                            </div>
                            <span id="strength-text" class="text-xs font-medium"></span>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="new_password_confirmation" class="block mb-1 text-sm font-medium text-gray-700">
                        Confirmar Nueva Contraseña *
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            name="new_password_confirmation"
                            id="new_password_confirmation"
                            required
                            placeholder="Repita la nueva contraseña"
                            class="w-full mt-1 px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                            onkeyup="checkPasswordMatch()"
                        />
                        <button
                            type="button"
                            onclick="togglePasswordVisibility('new_password_confirmation')"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                        >
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                    @error('new_password_confirmation')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    
                    {{-- Indicador de coincidencia --}}
                    <div id="password-match" class="mt-1 text-sm hidden">
                        <span id="match-text"></span>
                    </div>
                </div>
            </div>

            {{-- Consejos de seguridad --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                <h5 class="text-sm font-medium text-blue-900 mb-2 flex items-center">
                    <i data-lucide="shield" class="w-4 h-4 mr-1"></i>
                    Consejos para una contraseña segura:
                </h5>
                <ul class="text-xs text-blue-800 space-y-1">
                    <li>• Mínimo 8 caracteres de longitud</li>
                    <li>• Incluya mayúsculas y minúsculas</li>
                    <li>• Use números y símbolos especiales</li>
                    <li>• Evite información personal</li>
                </ul>
            </div>
        </div>

        {{-- Botones de acción --}}
        <div class="bg-white border-t border-gray-200 px-6 py-4 rounded-b-lg">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    <i data-lucide="info" class="w-4 h-4 inline mr-1"></i>
                    Su sesión se cerrará automáticamente después del cambio
                </div>
                
                <div class="flex space-x-3">
                    <button
                        type="button"
                        onclick="resetPasswordForm()"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                    >
                        <i data-lucide="rotate-ccw" class="w-4 h-4 mr-1 inline"></i>
                        Limpiar
                    </button>
                    
                    <button
                        type="submit"
                        class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        id="password-submit-btn"
                    >
                        <span class="submit-text">
                            <i data-lucide="key" class="w-4 h-4 mr-1 inline"></i>
                            Actualizar Contraseña
                        </span>
                        <span class="loading-text hidden">
                            <svg class="animate-spin w-4 h-4 mr-1 inline" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Actualizando...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.setAttribute('data-lucide', 'eye-off');
    } else {
        input.type = 'password';
        icon.setAttribute('data-lucide', 'eye');
    }
    
    // Reinicializar iconos de Lucide
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function checkPasswordStrength(password) {
    const strengthIndicator = document.getElementById('password-strength');
    const strengthBar = document.getElementById('strength-bar');
    const strengthText = document.getElementById('strength-text');
    
    if (!password) {
        strengthIndicator.classList.add('hidden');
        return;
    }
    
    strengthIndicator.classList.remove('hidden');
    
    let strength = 0;
    let feedback = [];
    
    // Criterios de fortaleza
    if (password.length >= 8) strength++;
    else feedback.push('mínimo 8 caracteres');
    
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    else feedback.push('mayúsculas y minúsculas');
    
    if (/\d/.test(password)) strength++;
    else feedback.push('números');
    
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    else feedback.push('símbolos especiales');
    
    // Actualizar visualización
    const colors = ['bg-red-500', 'bg-yellow-500', 'bg-blue-500', 'bg-green-500'];
    const texts = ['Débil', 'Regular', 'Buena', 'Excelente'];
    const widths = ['25%', '50%', '75%', '100%'];
    
    strengthBar.className = `h-1 rounded-full transition-all duration-300 ${colors[strength - 1] || 'bg-gray-300'}`;
    strengthBar.style.width = widths[strength - 1] || '0%';
    strengthText.textContent = texts[strength - 1] || '';
    strengthText.className = `text-xs font-medium ${colors[strength - 1]?.replace('bg-', 'text-') || 'text-gray-500'}`;
}

function checkPasswordMatch() {
    const password = document.getElementById('new_password').value;
    const confirmation = document.getElementById('new_password_confirmation').value;
    const matchIndicator = document.getElementById('password-match');
    const matchText = document.getElementById('match-text');
    
    if (!confirmation) {
        matchIndicator.classList.add('hidden');
        return;
    }
    
    matchIndicator.classList.remove('hidden');
    
    if (password === confirmation) {
        matchText.textContent = '✓ Las contraseñas coinciden';
        matchText.className = 'text-green-600';
    } else {
        matchText.textContent = '✗ Las contraseñas no coinciden';
        matchText.className = 'text-red-600';
    }
}

function resetPasswordForm() {
    if (confirm('¿Está seguro de que desea limpiar todos los campos?')) {
        document.getElementById('password-form').reset();
        document.getElementById('password-strength').classList.add('hidden');
        document.getElementById('password-match').classList.add('hidden');
    }
}

// Loading state para el formulario de contraseña
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('password-form');
    const submitBtn = document.getElementById('password-submit-btn');
    
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            // Validar que las contraseñas coincidan
            const password = document.getElementById('new_password').value;
            const confirmation = document.getElementById('new_password_confirmation').value;
            
            if (password !== confirmation) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
                return;
            }
            
            submitBtn.disabled = true;
            submitBtn.querySelector('.submit-text').classList.add('hidden');
            submitBtn.querySelector('.loading-text').classList.remove('hidden');
        });
    }
});
</script>
