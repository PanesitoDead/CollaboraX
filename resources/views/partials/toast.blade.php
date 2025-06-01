{{-- Toast de Éxito --}}
@if(session('success'))
    <div
        id="toast-success"
        class="fixed top-4 right-4 bg-green-600 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center space-x-3 z-50
               opacity-0 -translate-y-2 transform transition-all duration-500"
    >
        {{-- Icono de check --}}
        <div class="flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-6 w-6 text-white"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor"
            >
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                />
            </svg>
        </div>

        {{-- Texto del mensaje --}}
        <div class="flex-1 font-semibold">
            {{ session('success') }}
        </div>

        {{-- Botón de cierre manual --}}
        <button
            onclick="closeToast('toast-success')"
            class="flex-shrink-0 focus:outline-none hover:text-gray-200 transition-colors duration-200"
        >
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-5 w-5 text-white"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor"
            >
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"
                />
            </svg>
        </button>
    </div>
@endif

{{-- Toast de Error --}}
@if(session('error'))
    <div
        id="toast-error"
        class="fixed top-4 right-4 bg-red-600 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center space-x-3 z-50
               opacity-0 -translate-y-2 transform transition-all duration-500"
    >
        {{-- Icono de alerta --}}
        <div class="flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-6 w-6 text-white"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor"
            >
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 8v4m0 4h.01M5.121 19.071A9.003 9.003 0 0112 3a9.003 9.003 0 016.879 16.071M12 21.75a9.752 9.752 0 01-7.071-2.929A9.752 9.752 0 0112 6.071a9.752 9.752 0 017.071 13.75A9.752 9.752 0 0112 21.75z"
                />
            </svg>
        </div>

        {{-- Texto del mensaje --}}
        <div class="flex-1 font-semibold">
            {{ session('error') }}
        </div>

        {{-- Botón de cierre manual --}}
        <button
            onclick="closeToast('toast-error')"
            class="flex-shrink-0 focus:outline-none hover:text-gray-200 transition-colors duration-200"
        >
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-5 w-5 text-white"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor"
            >
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"
                />
            </svg>
        </button>
    </div>
@endif

{{-- Script para animar entrada/salida de todos los toasts --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Función genérica para animar la entrada y salida de cualquier toast
        function animateToast(id) {
            const toast = document.getElementById(id);
            if (!toast) return;

            // 1) Hacer fade-in y slide-down: removemos las clases iniciales
            setTimeout(() => {
                toast.classList.remove('opacity-0', '-translate-y-2');
                toast.classList.add('opacity-100', 'translate-y-0');
            }, 50); // retraso pequeño para que la transición funcione

            // 2) Después de 3s, hacer fade-out y slide-up, y luego remover del DOM
            setTimeout(() => {
                toast.classList.remove('opacity-100', 'translate-y-0');
                toast.classList.add('opacity-0', '-translate-y-2');

                // Una vez termine la transición (500ms), lo quitamos del DOM
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.remove();
                    }
                }, 500);
            }, 3000);
        }

        // Si existe toast-success, animarlo
        if (document.getElementById('toast-success')) {
            animateToast('toast-success');
        }

        // Si existe toast-error, animarlo
        if (document.getElementById('toast-error')) {
            animateToast('toast-error');
        }
    });

    // Permite cerrar manualmente antes de los 3 segundos
    function closeToast(id) {
        const toast = document.getElementById(id);
        if (!toast) return;

        // Quitar clases de visible y poner de oculto
        toast.classList.remove('opacity-100', 'translate-y-0');
        toast.classList.add('opacity-0', '-translate-y-2');

        // Quitarlo del DOM tras la transición (500ms)
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 500);
    }
</script>
