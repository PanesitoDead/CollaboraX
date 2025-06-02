<div
  id="modalCambioEstado"
  class="fixed inset-0 w-screen h-screen bg-black/50 hidden z-50 opacity-0 transition-opacity duration-300"
  role="dialog"
  aria-modal="true"
  aria-labelledby="tituloModalCambioEstado"
>
  <div class="absolute inset-0" onclick="cerrarModalCambioEstado()"></div>

  <!-- Contenedor centrado -->
  <div class="fixed inset-0 flex items-center justify-center p-4">
    <div
      id="contenidoCambioEstado"
      class="bg-white rounded-2xl shadow-xl w-full max-w-md flex flex-col overflow-hidden min-h-0 transform scale-95 transition-transform duration-300"
    >
      <!-- Formulario para cambiar estado -->
      <form
        id="formCambioEstado"
        method="POST"
        class="flex flex-col"
      >
        @csrf
        <div id="campoMetodoCambioEstado"></div>
        <!-- action se asignará dinámicamente -->

        <!-- Cabecera fija -->
        <header
          class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center flex-shrink-0"
        >
          <h3 id="tituloModalCambioEstado" class="text-lg font-semibold text-gray-900">
            Confirmar acción
          </h3>
          <button
            type="button"
            onclick="cerrarModalCambioEstado()"
            aria-label="Cerrar modal"
            class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
          >
            <i data-lucide="x" class="w-6 h-6 text-gray-500"></i>
          </button>
        </header>

        <!-- Mensaje de advertencia -->
        <div class="px-6 py-4 flex-1">
          <p id="mensajeCambioEstado" class="text-gray-800 text-sm">
            <!-- Se llena dinámicamente con JS -->
          </p>
        </div>

        <!-- Pie de página fijo con botones -->
        <footer
          class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end space-x-2 flex-shrink-0"
        >
          <button
            type="button"
            onclick="cerrarModalCambioEstado()"
            class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            Cancelar
          </button>
          <button
            type="submit"
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
          >
            <span id="textoConfirmarCambio">Confirmar</span>
          </button>
        </footer>
      </form>
    </div>
  </div>
</div>

<script>
  let coordinadorCambioId = null;

  function abrirModalConfirmacion(id, activo) {
    coordinadorCambioId = id;

    // 1) Construir mensaje dinámico
    const mensajeEl = document.getElementById('mensajeCambioEstado');
    const textoBotonConfirmar = document.getElementById('textoConfirmarCambio');

    if (activo) {
        // Si está activo → desactivar
        textoBotonConfirmar.textContent = 'Desactivar';
        mensajeEl.innerHTML = `
            <div class="flex flex-col items-center space-y-4 py-6">
            <i data-lucide="alert-circle" class="w-12 h-12 text-red-500"></i>
            <p class="text-gray-800 text-center text-lg">
                ¿Está seguro que desea<br>
                <span class="font-semibold text-red-600">desactivar</span><br>
                este coordinador de equipo?
            </p>
            </div>
        `;
        } else {
        // Si está inactivo → activar
        textoBotonConfirmar.textContent = 'Activar';
        mensajeEl.innerHTML = `
            <div class="flex flex-col items-center space-y-4 py-6">
            <i data-lucide="check-circle" class="w-12 h-12 text-green-500"></i>
            <p class="text-gray-800 text-center text-lg">
                ¿Está seguro que desea<br>
                <span class="font-semibold text-green-600">activar</span><br>
                este coordinador de equipo?
            </p>
            </div>
        `;
        }

    // Actualizamos los iconos
    lucide.createIcons();

    // 2) Ajustar el action del formulario y _method (PATCH) con el nuevo estado
    const form = document.getElementById('formCambioEstado');
    form.action = `/admin/coordinadores-equipos/${id}/cambiar-estado`;

    const methodField = document.getElementById('campoMetodoCambioEstado');
    methodField.innerHTML = `<input type="hidden" name="_method" value="PATCH">`;

    // 3) Insertar campo oculto con el nuevo valor de 'activo'
    //    Si estaba activo (true), enviamos 'activo' = 0 (para desactivar). Si estaba inactivo, enviamos 'activo' = 1.
    const valorNuevo = activo ? '0' : '1';
    // Primero borramos cualquier input anterior, si existiera
    const inputExistente = document.getElementById('inputNuevoActivo');
    if (inputExistente) inputExistente.remove();

    const inputOculto = document.createElement('input');
    inputOculto.type = 'hidden';
    inputOculto.name = 'activo';
    inputOculto.id = 'inputNuevoActivo';
    inputOculto.value = valorNuevo;
    form.appendChild(inputOculto);

    // 4) Mostrar modal con animación idéntica a los demás
    const modal = document.getElementById('modalCambioEstado');
    const contenido = document.getElementById('contenidoCambioEstado');
    modal.classList.remove('hidden');
    // Retardo mínimo para aplicar transición
    setTimeout(() => {
      modal.classList.remove('opacity-0');
      contenido.classList.remove('scale-95');
    }, 10);

    document.body.style.overflow = 'hidden';
  }

  /**
   * Cierra el modal de confirmación de cambio de estado con animación inversa.
   */
  function cerrarModalCambioEstado() {
    const modal = document.getElementById('modalCambioEstado');
    const contenido = document.getElementById('contenidoCambioEstado');

    modal.classList.add('opacity-0');
    contenido.classList.add('scale-95');

    setTimeout(() => {
      modal.classList.add('hidden');
      document.body.style.overflow = 'auto';
      coordinadorCambioId = null;
      // Limpiar el campo _method y el input oculto para la próxima vez
      document.getElementById('campoMetodoCambioEstado').innerHTML = '';
      const inputNuevoActivo = document.getElementById('inputNuevoActivo');
      if (inputNuevoActivo) inputNuevoActivo.remove();
    }, 300);
  }

  // Cerrar modal con la tecla "Escape"
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && coordinadorCambioId !== null) {
      cerrarModalCambioEstado();
    }
  });
</script>