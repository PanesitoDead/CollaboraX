<!-- Modal para cambio de rol -->
<div
  id="modalCambioRol"
  class="fixed inset-0 w-screen h-screen bg-black/50 hidden z-50 opacity-0 transition-opacity duration-300"
  role="dialog"
  aria-modal="true"
  aria-labelledby="tituloModalCambioRol"
>
  <!-- Overlay semitransparente (clic aquí cierra modal) -->
  <div class="absolute inset-0" onclick="cerrarModalCambioRol()"></div>

  <!-- Contenedor centrado -->
  <div class="fixed inset-0 flex items-center justify-center p-4">
    <div
      id="contenidoModalCambioRol"
      class="bg-white rounded-2xl shadow-xl w-full max-w-lg flex flex-col overflow-hidden min-h-0 transform scale-95 transition-transform duration-300"
    >
      <form
        id="formCambioRol"
        method="POST"
        class="flex flex-col h-full"
      >
        @csrf
        <div id="campoMetodoCambioRol"></div>
        <!-- El atributo action se asignará dinámicamente en JS -->

        <!-- Cabecera fija -->
        <header
          class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center flex-shrink-0"
        >
          <h3 id="tituloModalCambioRol" class="text-lg font-semibold text-gray-900">
            Cambiar Rol de Usuario
          </h3>
          <button
            type="button"
            onclick="cerrarModalCambioRol()"
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
          <!-- Mostrar rol actual -->
          <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">
              Rol Actual:
            </label>
            <p id="rolActualTexto" class="text-gray-800 text-base font-medium"></p>
          </div>

          <!-- Selección de nuevo rol -->
          <div>
            <label
              for="selectNuevoRol"
              class="block mb-1 text-sm font-medium text-gray-700"
            >
              Nuevo Rol
            </label>
            <select
              name="rol"
              id="selectNuevoRol"
              required
              class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">-- Seleccionar rol --</option>
              <!-- Opciones de ejemplo; reemplazar por roles reales desde el backend -->
              <option value="administrador">Administrador</option>
              <option value="editor">Editor</option>
              <option value="usuario">Usuario</option>
              <option value="invitado">Invitado</option>
            </select>
          </div>

          <!-- Mensaje de advertencia dinámico -->
          <div id="mensajeCambioRol" class="text-center"></div>
        </div>

        <!-- Pie de página fijo -->
        <footer
          class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end space-x-2 flex-shrink-0"
        >
          <button
            type="button"
            onclick="cerrarModalCambioRol()"
            class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            Cancelar
          </button>
          <button
            type="submit"
            id="botonConfirmarCambio"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
            disabled
          >
            <span id="textoConfirmarCambio">Confirmar Cambio</span>
          </button>
        </footer>
      </form>
    </div>
  </div>
</div>

<script>
  let usuarioCambioId = null;
  let rolActualUsuario = "";

  /**
   * Abre el modal para cambiar el rol de un usuario.
   * @param {number|string} id               - ID del usuario.
   * @param {string} rolActual               - Texto del rol actual del usuario.
   */
  function abrirModalCambioRol(id, rolActual) {
    usuarioCambioId = id;
    rolActualUsuario = rolActual;

    // Actualizar texto del rol actual
    document.getElementById('rolActualTexto').textContent = rolActual;

    // Ajustar action del formulario y _method (PUT)
    const form = document.getElementById('formCambioRol');
    form.action = `/usuarios/${id}/cambiar-rol`;

    const methodField = document.getElementById('campoMetodoCambioRol');
    methodField.innerHTML = `<input type="hidden" name="_method" value="PUT">`;

    // Preseleccionar el rol actual en el <select>, si coincide el value
    const selectRol = document.getElementById('selectNuevoRol');
    selectRol.value = ""; // dejar vacío para obligar a elegir activamente
    updateMensajeYBoton(); // limpiar mensaje y deshabilitar botón

    // Agregar listener para detectar cambios en la selección
    selectRol.addEventListener('change', updateMensajeYBoton);

    // Mostrar modal con animación
    const modal = document.getElementById('modalCambioRol');
    const contenido = document.getElementById('contenidoModalCambioRol');

    modal.classList.remove('hidden');
    // Retardo mínimo para aplicar transición
    setTimeout(() => {
      modal.classList.remove('opacity-0');
      contenido.classList.remove('scale-95');
    }, 10);

    // Evitar scroll del body mientras el modal esté abierto
    document.body.style.overflow = 'hidden';

    // Refrescar íconos de Lucide
    if (typeof lucide !== 'undefined') lucide.createIcons();
  }

  /**
   * Actualiza el mensaje de advertencia y habilita/deshabilita el botón de confirmación.
   */
  function updateMensajeYBoton() {
    const selectRol = document.getElementById('selectNuevoRol');
    const nuevoRol = selectRol.value;
    const mensajeDiv = document.getElementById('mensajeCambioRol');
    const botonConfirm = document.getElementById('botonConfirmarCambio');

    // Si no se ha seleccionado rol o se selecciona igual al actual, deshabilitar botón
    if (!nuevoRol || nuevoRol === rolActualUsuario.toLowerCase()) {
      mensajeDiv.innerHTML = '';
      botonConfirm.disabled = true;
      return;
    }

    // Construir mensaje de advertencia
    mensajeDiv.innerHTML = `
      <div class="flex flex-col items-center space-y-4 py-4">
        <i data-lucide="alert-circle" class="w-10 h-10 text-yellow-500"></i>
        <p class="text-gray-800 text-center text-lg">
          ¿Está seguro que desea cambiar el rol de<br>
          <span class="font-semibold text-red-600">"${rolActualUsuario}"</span><br>
          a<br>
          <span class="font-semibold text-red-600">"${capitalize(nuevoRol)}"</span>?
        </p>
      </div>
    `;
    // Refrescar íconos de Lucide
    if (typeof lucide !== 'undefined') lucide.createIcons();

    // Habilitar botón de confirmar
    botonConfirm.disabled = false;
  }

  /**
   * Función para cerrar el modal de cambio de rol con animación inversa.
   */
  function cerrarModalCambioRol() {
    const modal = document.getElementById('modalCambioRol');
    const contenido = document.getElementById('contenidoModalCambioRol');

    modal.classList.add('opacity-0');
    contenido.classList.add('scale-95');

    setTimeout(() => {
      modal.classList.add('hidden');
      document.body.style.overflow = 'auto';
      usuarioCambioId = null;
      rolActualUsuario = "";
      // Limpiar _method y mensaje para la próxima vez
      document.getElementById('campoMetodoCambioRol').innerHTML = '';
      document.getElementById('mensajeCambioRol').innerHTML = '';
      document.getElementById('selectNuevoRol').removeEventListener('change', updateMensajeYBoton);
      document.getElementById('selectNuevoRol').value = "";
      document.getElementById('botonConfirmarCambio').disabled = true;
    }, 300);
  }

  // Cerrar modal cuando se presiona la tecla "Escape"
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && usuarioCambioId !== null) {
      cerrarModalCambioRol();
    }
  });

  /**
   * Capitaliza la primera letra de una cadena.
   * @param {string} str
   * @returns {string}
   */
  function capitalize(str) {
    if (!str) return "";
    return str.charAt(0).toUpperCase() + str.slice(1);
  }
</script>
