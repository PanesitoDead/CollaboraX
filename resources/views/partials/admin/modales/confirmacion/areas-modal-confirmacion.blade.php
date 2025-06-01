<div
  id="modalEliminarArea"
  class="fixed inset-0 w-screen h-screen bg-black/50 hidden z-50 opacity-0 transition-opacity duration-300"
  role="dialog"
  aria-modal="true"
  aria-labelledby="tituloModalEliminarArea"
>
  <!-- Overlay semitransparente (clic aquí cierra modal) -->
  <div class="absolute inset-0" onclick="cerrarModalEliminarArea()"></div>

  <!-- Contenedor centrado -->
  <div class="fixed inset-0 flex items-center justify-center p-4">
    <div
      id="contenidoEliminarArea"
      class="bg-white rounded-2xl shadow-xl w-full max-w-md flex flex-col overflow-hidden min-h-0 transform scale-95 transition-transform duration-300"
    >
      <!-- Formulario para eliminar área -->
      <form
        id="formEliminarArea"
        method="POST"
        class="flex flex-col"
      >
        @csrf
        <div id="campoMetodoEliminarArea"></div>
        <!-- El atributo action se asignará dinámicamente -->

        <!-- Cabecera fija -->
        <header
          class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center flex-shrink-0"
        >
          <h3 id="tituloModalEliminarArea" class="text-lg font-semibold text-gray-900">
            Confirmar eliminación
          </h3>
          <button
            type="button"
            onclick="cerrarModalEliminarArea()"
            aria-label="Cerrar modal"
            class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
          >
            <i data-lucide="x" class="w-6 h-6 text-gray-500"></i>
          </button>
        </header>

        <!-- Mensaje de advertencia -->
        <div class="px-6 py-4 flex-1">
          <p id="mensajeEliminarArea" class="text-gray-800 text-sm">
            <!-- Se llena dinámicamente con JS -->
          </p>
        </div>

        <!-- Pie de página fijo con botones -->
        <footer
          class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end space-x-2 flex-shrink-0"
        >
          <button
            type="button"
            onclick="cerrarModalEliminarArea()"
            class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            Cancelar
          </button>
          <button
            type="submit"
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
          >
            <span id="textoConfirmarEliminar">Eliminar</span>
          </button>
        </footer>
      </form>
    </div>
  </div>
</div>

<script>
  let areaEliminarId = null;

  /**
   * Abre el modal de confirmación para eliminar un área.
   * @param {number|string} id   - ID del área a eliminar.
   * @param {string} nombreArea  - Nombre del área (para mostrar en el mensaje).
   */
  function abrirModalEliminarArea(id, nombreArea) {
    areaEliminarId = id;

    // 1) Construir mensaje dinámico con icono y texto
    const mensajeEl = document.getElementById('mensajeEliminarArea');
    const textoBotonEliminar = document.getElementById('textoConfirmarEliminar');

    textoBotonEliminar.textContent = 'Eliminar';

    mensajeEl.innerHTML = `
      <div class="flex flex-col items-center space-y-4 py-6">
        <i data-lucide="trash-2" class="w-12 h-12 text-red-500"></i>
        <p class="text-gray-800 text-center text-lg">
          ¿Está seguro que desea<br>
          <span class="font-semibold text-red-600">eliminar</span><br>
          el área "<span class="italic">${nombreArea}</span>"?
        </p>
      </div>
    `;

    // Actualizamos los iconos de Lucide (asegúrate de haber incluido lucide.createIcons() en tu proyecto)
    lucide.createIcons();

    // 2) Ajustar el action del formulario y _method (DELETE)
    const form = document.getElementById('formEliminarArea');
    form.action = `/admin/areas/${id}`;

    const methodField = document.getElementById('campoMetodoEliminarArea');
    methodField.innerHTML = `<input type="hidden" name="_method" value="DELETE">`;

    // 3) Mostrar el modal con animación idéntica a los demás
    const modal = document.getElementById('modalEliminarArea');
    const contenido = document.getElementById('contenidoEliminarArea');
    modal.classList.remove('hidden');
    // Retardo mínimo para aplicar transición
    setTimeout(() => {
      modal.classList.remove('opacity-0');
      contenido.classList.remove('scale-95');
    }, 10);

    document.body.style.overflow = 'hidden';
  }

  /**
   * Cierra el modal de confirmación de eliminación con animación inversa.
   */
  function cerrarModalEliminarArea() {
    const modal = document.getElementById('modalEliminarArea');
    const contenido = document.getElementById('contenidoEliminarArea');

    modal.classList.add('opacity-0');
    contenido.classList.add('scale-95');

    setTimeout(() => {
      modal.classList.add('hidden');
      document.body.style.overflow = 'auto';
      areaEliminarId = null;
      // Limpiar el campo _method para la próxima vez
      document.getElementById('campoMetodoEliminarArea').innerHTML = '';
    }, 300);
  }

  // Cerrar modal cuando se presiona la tecla "Escape"
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && areaEliminarId !== null) {
      cerrarModalEliminarArea();
    }
  });
</script>