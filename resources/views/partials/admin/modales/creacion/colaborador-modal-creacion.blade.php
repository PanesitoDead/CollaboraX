<div
  id="modalColaborador"
  class="fixed inset-0 w-screen h-screen bg-black/50 hidden z-50 opacity-0 transition-opacity duration-300"
  role="dialog"
  aria-modal="true"
  aria-labelledby="tituloModalColaborador"
>
  <!-- Capa semitransparente (clic aquí cierra modal) -->
  <div class="absolute inset-0" onclick="cerrarModalColaborador()"></div>

  <!-- Contenedor centrado -->
  <div class="fixed inset-0 flex items-center justify-center p-4">
    <div
      id="contenidoModalColaborador"
      class="bg-white rounded-2xl shadow-xl w-full max-w-lg flex flex-col overflow-hidden min-h-0 transform scale-95 transition-transform duration-300"
    >
      <form
        id="formularioColaborador"
        method="POST"
        action="{{ route('admin.colaboradores.store') }}"
        class="flex flex-col h-full"
      >
        @csrf
        <!-- Cabecera fija -->
        <header
          class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center flex-shrink-0"
        >
          <h3 id="tituloModalColaborador" class="text-lg font-semibold text-gray-900">
            Crear Colaborador
          </h3>
          <button
            type="button"
            onclick="cerrarModalColaborador()"
            aria-label="Cerrar modal"
            class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
          >
            <i data-lucide="x" class="w-6 h-6 text-gray-500"></i>
          </button>
        </header>

        <!-- Contenido scrollable -->
        <div
          class="px-6 py-4 space-y-4 overflow-y-auto flex-1 max-h-[70vh] min-h-0"
        >
          <!-- Nombres -->
          <div>
            <label
              for="inputNombresColaborador"
              class="block mb-1 text-sm font-medium text-gray-700"
            >
              Nombres
            </label>
            <input
              type="text"
              name="nombres"
              id="inputNombresColaborador"
              required
              class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Apellido Paterno -->
            <div>
              <label
                for="inputApellidoPaternoColaborador"
                class="block mb-1 text-sm font-medium text-gray-700"
              >
                Apellido Paterno
              </label>
              <input
                type="text"
                name="apellido_paterno"
                id="inputApellidoPaternoColaborador"
                required
                class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            <!-- Apellido Materno -->
            <div>
              <label
                for="inputApellidoMaternoColaborador"
                class="block mb-1 text-sm font-medium text-gray-700"
              >
                Apellido Materno
              </label>
              <input
                type="text"
                name="apellido_materno"
                id="inputApellidoMaternoColaborador"
                required
                class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
          </div> 
          <!-- Correo personal -->
          <div>
            <label
              for="inputCorreoColaborador"
              class="block mb-1 text-sm font-medium text-gray-700"
            >
              Correo Personal
            </label>
            <input
              type="email"
              name="correo_personal"
              id="inputCorreoPersonal"
              required
              class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="micorreo@ejemplo.com"
            />
          </div>
          <!-- Correo generado -->
          <div>
            <label
              for="inputCorreoColaborador"
              class="block mb-1 text-sm font-medium text-gray-700"
            >
              Correo Corporativo
            </label>
            <div class="flex items-center">
              <input
                type="text"
                name="correo"
                id="inputCorreoColaborador"
                readonly
                class="flex-1 mt-1 px-3 py-2 border border-gray-300 rounded-l-lg bg-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="nombre.apellidoPaterno.apellidoMaterno"
              />
              <span
                id="dominioColaborador"
                class="mt-1 px-3 py-2 border border-l-0 border-gray-300 rounded-r-lg bg-gray-200 text-gray-700 select-none"
              >
                @nombreEmpresa.cx.com
              </span>
            </div>
          </div>

          <!-- Contraseña generada + botones -->
          <div>
            <label
              for="inputClaveColaborador"
              class="block mb-1 text-sm font-medium text-gray-700"
            >
              Contraseña Generada
            </label>
            <div class="flex items-center space-x-2">
              <input
                type="text"
                id="inputClaveColaborador"
                name="clave"
                readonly
                class="flex-1 mt-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
              <!-- Botón regenerar -->
              <button
                type="button"
                onclick="regenerarClave()"
                class="mt-1 p-2.5 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition"
                title="Regenerar contraseña"
              >
                <i data-lucide="refresh-ccw" class="w-5 h-5 text-gray-600"></i>
              </button>
              <!-- Botón copiar -->
              <button
                type="button"
                onclick="copiarClave()"
                class="mt-1 p-2.5 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition"
                title="Copiar contraseña"
              >
                <i data-lucide="clipboard" class="w-5 h-5 text-gray-600"></i>
              </button>
            </div>
            <p class="mt-2 text-xs text-gray-500">
              Nota: El usuario podrá cambiar esta contraseña una vez inicie sesión.
            </p>
          </div>
        </div>

        <!-- Pie de página fijo -->
        <footer
          class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end space-x-2 flex-shrink-0"
        >
          <button
            type="button"
            onclick="cerrarModalColaborador()"
            class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            Cancelar
          </button>
          <button
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          >
            Crear Colaborador
          </button>
        </footer>
      </form>
    </div>
  </div>
</div>

<script>
  function abrirModalCrearColaborador(dominio) {
    // Guardamos el dominio (sin arroba)
    document.getElementById('dominioColaborador').textContent = '@' + dominio + '.cx.com';

    // Limpiar campos
    document.getElementById('inputNombresColaborador').value = "";
    document.getElementById('inputApellidoPaternoColaborador').value = "";
    document.getElementById('inputApellidoMaternoColaborador').value = "";
    document.getElementById('inputCorreoPersonal').value = "";
    document.getElementById('inputCorreoColaborador').value = "";
    document.getElementById('inputClaveColaborador').value = "";

    // Generar una contraseña inicial
    document.getElementById('inputClaveColaborador').value = generarClave();

    // Mostrar modal con animación
    const modal = document.getElementById('modalColaborador');
    const contenido = document.getElementById('contenidoModalColaborador');
    modal.classList.remove('hidden');
    setTimeout(() => {
      modal.classList.remove('opacity-0');
      contenido.classList.remove('scale-95');
    }, 10);
    document.body.style.overflow = 'hidden';
  }

  /**
   * Cierra el modal de creación de colaborador.
   */
  function cerrarModalColaborador() {
    const modal = document.getElementById('modalColaborador');
    const contenido = document.getElementById('contenidoModalColaborador');
    modal.classList.add('opacity-0');
    contenido.classList.add('scale-95');
    setTimeout(() => {
      modal.classList.add('hidden');
      document.body.style.overflow = 'auto';
    }, 300);
  }

  /**
   * Genera una contraseña aleatoria de 8 caracteres (letras y números).
   * @returns {string}
   */
  function generarClave() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let pass = '';
    for (let i = 0; i < 8; i++) {
      pass += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return pass;
  }

  /**
   * Regenera la contraseña y la asigna al input.
   */
  function regenerarClave() {
    const nuevoPass = generarClave();
    document.getElementById('inputClaveColaborador').value = nuevoPass;
  }

  /**
   * Copia la contraseña actual al portapapeles.
   */
  function copiarClave() {
    const inputPass = document.getElementById('inputClaveColaborador');
    inputPass.select();
    inputPass.setSelectionRange(0, 99999); // Para móviles
    document.execCommand('copy');
    // Opcional: mostrar un breve tooltip o mensaje
    alert('Contraseña copiada al portapapeles.');
  }

  /**
   * Construye el correo corporativo en base a los campos Nombre, Apellidos, y dominio.
   */
  function actualizarCorreo() {
    const nombre = document.getElementById('inputNombresColaborador').value.trim();
    const apPaterno = document.getElementById('inputApellidoPaternoColaborador').value.trim();
    const apMaterno = document.getElementById('inputApellidoMaternoColaborador').value.trim();

    if (!nombre || !apPaterno || !apMaterno) {
      document.getElementById('inputCorreoColaborador').value = '';
      return;
    }

    // Formato: nombre.apellidoPaterno.apellidoMaterno@dominio
    const parteNombre = nombre.toLowerCase().replace(/\s+/g, '.');
    const parteApPaterno = apPaterno.toLowerCase().replace(/\s+/g, '');
    const parteApMaterno = apMaterno.toLowerCase().replace(/\s+/g, '');
    const correoGenerado = `${parteNombre}.${parteApPaterno}.${parteApMaterno}`;
    document.getElementById('inputCorreoColaborador').value = correoGenerado;
  }

  // Agregar event listeners a los inputs para recalcular el correo en vivo
  document
    .getElementById('inputNombresColaborador')
    .addEventListener('input', actualizarCorreo);
  document
    .getElementById('inputApellidoPaternoColaborador')
    .addEventListener('input', actualizarCorreo);
  document
    .getElementById('inputApellidoMaternoColaborador')
    .addEventListener('input', actualizarCorreo);

  // Cerrar modal al presionar "Escape"
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !document.getElementById('modalColaborador').classList.contains('hidden')) {
      cerrarModalColaborador();
    }
  });
</script>
