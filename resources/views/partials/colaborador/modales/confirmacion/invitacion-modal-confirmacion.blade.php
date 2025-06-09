<!-- Modal genérico para acciones de invitación -->
<div
  id="modalInvitacion"
  class="fixed inset-0 w-screen h-screen bg-black/50 hidden z-50 opacity-0 transition-opacity duration-300"
  role="dialog"
  aria-modal="true"
  aria-labelledby="tituloModalInvitacion"
>
  <div class="absolute inset-0" onclick="cerrarModalInvitacion()"></div>

  <!-- Contenedor centrado -->
  <div class="fixed inset-0 flex items-center justify-center p-4">
    <div
      id="contenidoInvitacion"
      class="bg-white rounded-2xl shadow-xl w-full max-w-md flex flex-col overflow-hidden min-h-0 transform scale-95 transition-transform duration-300"
    >
      <form
        id="formInvitacion"
        method="POST"
        class="flex flex-col"
      >
        @csrf
        <div id="campoMetodoInvitacion"></div>

        <!-- Cabecera -->
        <header
          class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center flex-shrink-0"
        >
          <h3 id="tituloModalInvitacion" class="text-lg font-semibold text-gray-900">
            Confirmar acción
          </h3>
          <button
            type="button"
            onclick="cerrarModalInvitacion()"
            aria-label="Cerrar modal"
            class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
          >
            <i data-lucide="x" class="w-6 h-6 text-gray-500"></i>
          </button>
        </header>

        <!-- Mensaje dinámico -->
        <div class="px-6 py-4 flex-1">
          <p id="mensajeInvitacion" class="text-gray-800 text-sm"></p>
        </div>

        <!-- Pie con botones -->
        <footer
          class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end space-x-2 flex-shrink-0"
        >
          <button
            type="button"
            onclick="cerrarModalInvitacion()"
            class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            Cancelar
          </button>
          <button
            type="submit"
            id="botonConfirmarInvitacion"
            class="px-4 py-2 rounded-lg hover:opacity-90"
          >
            <span id="textoConfirmarInvitacion">Confirmar</span>
          </button>
        </footer>
      </form>
    </div>
  </div>
</div>

<script>
  let invitacionIdActual = null;
  let tipoAccionActual = null; // 'aceptar' o 'rechazar'

  function abrirModalInvitacion(id, tipo) {
    invitacionIdActual = id;
    tipoAccionActual = tipo;

    const mensajeEl = document.getElementById('mensajeInvitacion');
    const textoBoton = document.getElementById('textoConfirmarInvitacion');
    const botonConfirmar = document.getElementById('botonConfirmarInvitacion');
    const form = document.getElementById('formInvitacion');
    const methodField = document.getElementById('campoMetodoInvitacion');

    // Ajustar texto, icono y estilos según acción
    if (tipo === 'aceptar') {
      textoBoton.textContent = 'Aceptar';
      botonConfirmar.classList.remove('bg-red-600', 'text-white');
      botonConfirmar.classList.add('bg-blue-600', 'text-white');
      mensajeEl.innerHTML = `
        <div class="flex flex-col items-center space-y-4 py-6">
          <i data-lucide="check-circle" class="w-12 h-12 text-blue-500"></i>
          <p class="text-gray-800 text-center text-lg">
            ¿Está seguro que desea<br>
            <span class="font-semibold text-blue-600">aceptar</span><br>
            esta invitación?
          </p>
        </div>
      `;
    } else {
      textoBoton.textContent = 'Rechazar';
      botonConfirmar.classList.remove('bg-blue-600', 'text-white');
      botonConfirmar.classList.add('bg-red-600', 'text-white');
      mensajeEl.innerHTML = `
        <div class="flex flex-col items-center space-y-4 py-6">
          <i data-lucide="x-circle" class="w-12 h-12 text-red-500"></i>
          <p class="text-gray-800 text-center text-lg">
            ¿Está seguro que desea<br>
            <span class="font-semibold text-red-600">rechazar</span><br>
            esta invitación?
          </p>
        </div>
      `;
    }

    // Reconstruir action y método
    form.action = `/colaborador/invitaciones/${id}/${tipo}`;
    methodField.innerHTML = `<input type="hidden" name="_method" value="PATCH">`;

    // Refrescar iconos Lucide
    lucide.createIcons();

    // Mostrar modal con animación
    const modal = document.getElementById('modalInvitacion');
    const contenido = document.getElementById('contenidoInvitacion');
    modal.classList.remove('hidden');
    setTimeout(() => {
      modal.classList.remove('opacity-0');
      contenido.classList.remove('scale-95');
    }, 10);
    document.body.style.overflow = 'hidden';
  }

  function cerrarModalInvitacion() {
    const modal = document.getElementById('modalInvitacion');
    const contenido = document.getElementById('contenidoInvitacion');
    modal.classList.add('opacity-0');
    contenido.classList.add('scale-95');
    setTimeout(() => {
      modal.classList.add('hidden');
      document.body.style.overflow = 'auto';
      invitacionIdActual = null;
      tipoAccionActual = null;
      document.getElementById('campoMetodoInvitacion').innerHTML = '';
    }, 300);
  }

  // Cerrar con tecla Escape
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && invitacionIdActual !== null) {
      cerrarModalInvitacion();
    }
  });
</script>
