<div
  id="modalArea"
  class="fixed inset-0 w-screen h-screen bg-black/50 hidden z-50 opacity-0 transition-opacity duration-300"
  role="dialog"
  aria-modal="true"
  aria-labelledby="tituloModalArea"
>
  <!-- Capa semitransparente (clic aquí cierra modal) -->
  <div class="absolute inset-0" onclick="cerrarAreaModal()"></div>
  <!-- Contenedor centrado -->
  <div class="fixed inset-0 flex items-center justify-center p-4">
    <div
      id="contenidoModalArea"
      class="bg-white rounded-2xl shadow-xl w-full max-w-lg flex flex-col overflow-hidden min-h-0 transform scale-95 transition-transform duration-300"
    >
      <form
        id="areaForm"
        method="POST"
        class="flex flex-col h-full"
      >
        @csrf
        <div id="campoMetodoArea"></div>
        <!-- El atributo action se asignará dinámicamente en JS para POST/PUT -->

        <!-- Cabecera fija -->
        <header class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center flex-shrink-0">
          <h3 id="tituloModalArea" class="text-lg font-semibold text-gray-900">
            Nueva Área
          </h3>
          <button
            type="button"
            onclick="cerrarAreaModal()"
            aria-label="Cerrar modal"
            class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
          >
            <i data-lucide="x" class="w-6 h-6 text-gray-500"></i>
          </button>
        </header>

        <!-- Contenido scrollable -->
        <div class="px-6 py-4 space-y-4 overflow-y-auto flex-1 max-h-[70vh] min-h-0">
          <!-- fila 1: Nombre y Código -->
          <div id="camposGeneralesArea" class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
              <div>
                <label
                  for="inputNombreArea"
                  class="block mb-1 text-sm font-medium text-gray-700"
                >
                  Nombre del Área
                </label>
                <input
                  type="text"
                  name="nombre"
                  id="inputNombreArea"
                  placeholder="Ej: Marketing, Ventas"
                  required
                  class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
              <div>
                <label
                  for="inputCodigoArea"
                  class="block mb-1 text-sm font-medium text-gray-700"
                >
                  Código
                </label>
                <input
                  type="text"
                  name="codigo"
                  id="inputCodigoArea"
                  required
                  placeholder="Ej: MKT, VNT"
                  class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
            </div>
            <!-- Descripción -->
            <div>
              <label
                for="inputDescripcionArea"
                class="block mb-1 text-sm font-medium text-gray-700"
              >
                Descripción
              </label>
              <textarea
                name="descripcion"
                id="inputDescripcionArea"
                rows="3"
                placeholder="Describe responsabilidades y objetivos..."
                class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              ></textarea>
            </div>
            <!-- Color y Estado en dos columnas -->
            <div class="grid gap-4 md:grid-cols-2">
              <div>
                <label
                  for="selectColorArea"
                  class="block mb-1 text-sm font-medium text-gray-700"
                >
                  Color
                </label>
                <select
                  name="color"
                  id="selectColorArea"
                  required
                  class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="">Seleccionar color</option>
                  <option value="blue">Azul</option>
                  <option value="green">Verde</option>
                  <option value="red">Rojo</option>
                  <option value="yellow">Amarillo</option>
                  <option value="purple">Morado</option>
                  <option value="orange">Naranja</option>
                </select>
              </div>
              <div>
                <label
                  for="selectEstadoArea"
                  class="block mb-1 text-sm font-medium text-gray-700"
                >
                  Estado
                </label>
                <select
                  name="activo"
                  id="selectEstadoArea"
                  required
                  class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="1">Activa</option>
                  <option value="0">Inactiva</option>
                </select>
              </div>
            </div>
          </div>
          <!-- Coordinador General (Opcional) -->
          <div class="mb-4">
            <label for="selectCoordinadorArea" class="block mb-1 text-sm font-medium text-gray-700">
              Coordinador General (Opcional)
            </label>
            <div class="flex items-center space-x-2">
              {{-- Input de solo lectura que muestra el nombre del colaborador --}}
              <input
                type="text"
                id="inputMostrarCoordinador"
                readonly
                value="{{ $area->coordinador->nombre ?? '' }}"
                placeholder="Ningún colaborador seleccionado"
                class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-700 cursor-not-allowed"
              />
              <button
                type="button"
                onclick="abrirModalColaboradores()"
                class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                Buscar
              </button>
            </div>
            {{--  Campo oculto que lleva el ID real al servidor --}}
            <input
              type="hidden"
              name="coordinador_id"
              id="inputHiddenCoordinador"
              value="{{ $area->coordinador->id ?? '' }}"
            />
            <p class="mt-1 text-xs text-gray-500">
              Puedes asignar ahora o más tarde.
            </p>
          </div>
        </div>
        <!-- Pie de página fijo -->
        <footer class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end space-x-2 flex-shrink-0">
          <button
            type="button"
            onclick="cerrarAreaModal()"
            class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            Cancelar
          </button>
          <button
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          >
            <span id="textoEnviarArea">Crear Área</span>
          </button>
        </footer>
      </form>
    </div>
  </div>
</div>
@include('partials.admin.modales.busqueda.colaboradores-modal-busqueda')


<script>
  let idAreaActual = null;
  /**
   * Abre el modal de Área. 
   * Si recibe un id (número/string), asume edición y hace fetch para cargar datos.
   * Si id es null o undefined, abre en modo “crear” (POST).
   */
  async function abrirAreaModal(id, isAsignar = false) {
    idAreaActual = id;
    const formulario = document.getElementById('areaForm');
    const methodField = document.getElementById('campoMetodoArea');
    const titulo = document.getElementById('tituloModalArea');
    const textoSubmit = document.getElementById('textoEnviarArea');

    // Si es asignación de coordinador, no se usa el formulario de área
    if (isAsignar) {
      document.getElementById('camposGeneralesArea').classList.add('hidden');
    } else {
      document.getElementById('camposGeneralesArea').classList.remove('hidden');
    }

    if (idAreaActual) {
      // MODO EDICIÓN: PUT a /areas/{id}
      formulario.action = `/admin/areas/${idAreaActual}`;
      methodField.innerHTML = `<input type="hidden" name="_method" value="PUT">`;
      titulo.textContent = 'Editar Área';
      textoSubmit.textContent = 'Actualizar Área';

      try {
        const respuesta = await fetch(`/admin/areas/${idAreaActual}`);
        if (!respuesta.ok) throw new Error('No se recibieron datos del área');
        const data = await respuesta.json();

        // Rellenar valores en el formulario
        document.getElementById('inputNombreArea').value = data.nombre ?? '';
        document.getElementById('inputCodigoArea').value = data.codigo ?? '';
        document.getElementById('inputDescripcionArea').value = data.descripcion ?? '';
        document.getElementById('selectColorArea').value = data.color ?? '';
        document.getElementById('selectEstadoArea').value = data.activo? 1 : 0;
        document.getElementById('inputMostrarCoordinador').value = data.coordinador_nombres + ' ' + data.coordinador_apellido_paterno + ' ' + data.coordinador_apellido_materno;
        document.getElementById('inputHiddenCoordinador').value = data.coordinador_id?.toString() ?? '';
      } catch (error) {
        console.error(error);
        alert('Error al cargar los datos del área. Revisa la consola para más detalles.');
        return;
      }
    } else {
      // MODO CREACIÓN: POST a /areas
      formulario.action = `/admin/areas`;
      methodField.innerHTML = ''; // Sin campo _method
      titulo.textContent = 'Nueva Área';
      textoSubmit.textContent = 'Crear Área';

      // Limpiar cualquier valor previo en el formulario
      formulario.reset();
    }

    // Mostrar modal con animación
    const modal = document.getElementById('modalArea');
    const contenido = document.getElementById('contenidoModalArea');
    modal.classList.remove('hidden');
    setTimeout(() => {
      modal.classList.remove('opacity-0');
      contenido.classList.remove('scale-95');
    }, 10);

    document.body.style.overflow = 'hidden';
  }

  /**
   * Cierra el modal de Área con animación inversa.
   */
  function cerrarAreaModal() {
    const modal = document.getElementById('modalArea');
    const contenido = document.getElementById('contenidoModalArea');

    modal.classList.add('opacity-0');
    contenido.classList.add('scale-95');

    setTimeout(() => {
      modal.classList.add('hidden');
      document.body.style.overflow = 'auto';
      idAreaActual = null;
      document.getElementById('campoMetodoArea').innerHTML = '';
    }, 300);
  }

  // Cerrar modal cuando se presiona la tecla "Escape"
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && idAreaActual !== null) {
      cerrarAreaModal();
    }
  });

</script>
