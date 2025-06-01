<div
  id="modalEmpresa"
  class="fixed inset-0 w-screen h-screen bg-black/50 hidden z-50 opacity-0 transition-opacity duration-300"
  role="dialog"
  aria-modal="true"
  aria-labelledby="tituloModalEmpresa"
>
  <!-- Capa semitransparente (clic aquí cierra modal) -->
  <div class="absolute inset-0" onclick="cerrarModalEmpresa()"></div>

  <!-- Contenedor centrado -->
  <div class="fixed inset-0 flex items-center justify-center p-4">
    <div
      id="contenidoModalEmpresa"
      class="bg-white rounded-2xl shadow-xl w-full max-w-lg flex flex-col overflow-hidden min-h-0 transform scale-95 transition-transform duration-300"
    >
      <form
        id="formularioEmpresa"
        method="POST"
        enctype="multipart/form-data"
        class="flex flex-col h-full"
      >
        @csrf
        <div id="campoMetodoEmpresa"></div>
        <!-- El atributo action se asignará dinámicamente en JS -->

        <!-- Cabecera fija -->
        <header
          class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center flex-shrink-0"
        >
          <h3 id="tituloModalEmpresa" class="text-lg font-semibold text-gray-900">
            Editar Empresa
          </h3>
          <button
            type="button"
            onclick="cerrarModalEmpresa()"
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
          <!-- Selección de Plan -->
          <div>
            <label
              for="inputPlanEmpresa"
              class="block mb-1 text-sm font-medium text-gray-700"
            >
              Plan de Servicio
            </label>
            <select
              name="plan_servicio_id"
              id="inputPlanEmpresa"
              required
              class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">-- Seleccionar plan --</option>
              <option value="1">Estándar</option>
              <option value="2">Business</option>
              <option value="3">Enterprise</option>
            </select>
          </div>

          <!-- Nombre de la empresa -->
          <div>
            <label
              for="inputNombreEmpresa"
              class="block mb-1 text-sm font-medium text-gray-700"
            >
              Nombre
            </label>
            <input
              type="text"
              name="nombre"
              id="inputNombreEmpresa"
              required
              class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <!-- Descripción -->
          <div>
            <label
              for="inputDescripcionEmpresa"
              class="block mb-1 text-sm font-medium text-gray-700"
            >
              Descripción
            </label>
            <textarea
              name="descripcion"
              id="inputDescripcionEmpresa"
              rows="3"
              class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            ></textarea>
          </div>

          <!-- RUC y Teléfono en dos columnas -->
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label
                for="inputRucEmpresa"
                class="block mb-1 text-sm font-medium text-gray-700"
              >
                RUC
              </label>
              <input
                type="text"
                name="ruc"
                id="inputRucEmpresa"
                required
                class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            <div>
              <label
                for="inputTelefonoEmpresa"
                class="block mb-1 text-sm font-medium text-gray-700"
              >
                Teléfono
              </label>
              <input
                type="tel"
                name="telefono"
                id="inputTelefonoEmpresa"
                class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
          </div>

          <!-- Correo (se agregó para mostrar correo actual) -->
          <div>
            <label
              for="inputCorreoEmpresa"
              class="block mb-1 text-sm font-medium text-gray-700"
            >
              Correo
            </label>
            <input
              type="email"
              name="correo"
              id="inputCorreoEmpresa"
              required
              class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-100"
              readonly
            />
          </div>

          <!-- Estado (mostrar si está activo o no) -->
          <div>
            <label
              for="inputActivoEmpresa"
              class="block mb-1 text-sm font-medium text-gray-700"
            >
              Estado
            </label>
            <select
              name="activo"
              id="inputActivoEmpresa"
              required
              class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="1">Activo</option>
              <option value="0">Inactivo</option>
            </select>
          </div>

          <!-- Foto de perfil con vista previa -->
          <div>
            <label
              for="avatarEmpresa"
              class="block mb-1 text-sm font-medium text-gray-700"
            >
              Foto de Perfil
            </label>
            <div class="flex items-center space-x-4">
              <div class="w-18 h-18 rounded-full overflow-hidden border-4 border-gray-300 bg-gray-100 flex items-center justify-center relative">
                <img id="vistaPreviaAvatar" src="" alt="Vista previa del avatar de la empresa" class="w-full h-full object-cover absolute top-0 left-0 hidden rounded-full"/>
              </div>
              <div>
                <h4 id="nombreEmpresa" class="text-lg font-medium text-gray-900"></h4>
                <p id="emailEmpresa" class="text-sm text-gray-500"></p>
              </div>
              <div>
                <input
                  type="file"
                  name="avatar"
                  id="avatarEmpresa"
                  accept="image/*"
                  class="mt-1"
                />
                <p class="mt-1 text-xs text-gray-500">
                  Elige una nueva foto para actualizar.
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Pie de página fijo -->
        <footer
          class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end space-x-2 flex-shrink-0"
        >
          <button
            type="button"
            onclick="cerrarModalEmpresa()"
            class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            Cancelar
          </button>
          <button
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          >
            <span id="textoEnviarEmpresa">Actualizar Empresa</span>
          </button>
        </footer>
      </form>
    </div>
  </div>
</div>

<script>
  let idEmpresaActual = null;

  async function abrirModalEmpresa(id) {
    idEmpresaActual = id;

    const formulario = document.getElementById('formularioEmpresa');
    formulario.action = `/super-admin/empresas/${id}`;

    const methodField = document.getElementById('campoMetodoEmpresa');
    methodField.innerHTML = `<input type="hidden" name="_method" value="PUT">`;

    try {
      const respuesta = await fetch(`/super-admin/empresas/${id}`);
      if (!respuesta.ok) throw new Error('No se recibieron datos de la empresa');
      const data = await respuesta.json();

      document.getElementById('inputPlanEmpresa').value = data.plan_servicio_id?.toString() ?? '';
      document.getElementById('inputNombreEmpresa').value = data.nombre ?? '';
      document.getElementById('inputDescripcionEmpresa').value = data.descripcion ?? '';
      document.getElementById('inputRucEmpresa').value = data.ruc ?? '';
      document.getElementById('inputTelefonoEmpresa').value = data.telefono ?? '';
      document.getElementById('inputCorreoEmpresa').value = data.correo ?? '';
      document.getElementById('inputActivoEmpresa').value = data.activo ? '1' : '0';
      document.getElementById('vistaPreviaAvatar').src = '/images/default-avatar.png';

      // Mostrar modal con animación
      const modal = document.getElementById('modalEmpresa');
      const contenido = document.getElementById('contenidoModalEmpresa');

      modal.classList.remove('hidden');
      // Retardo mínimo para que las clases CSS de transición se apliquen
      setTimeout(() => {
        modal.classList.remove('opacity-0');
        contenido.classList.remove('scale-95');
      }, 10);

      document.body.style.overflow = 'hidden';
    }
    catch (error) {
      console.error(error);
      alert('Error al cargar los datos de la empresa. Revisa la consola para más detalles.');
    }
  }

  /**
   * Cierra el modal de edición de empresa con animación inversa.
   */
  function cerrarModalEmpresa() {
    const modal = document.getElementById('modalEmpresa');
    const contenido = document.getElementById('contenidoModalEmpresa');

    modal.classList.add('opacity-0');
    contenido.classList.add('scale-95');

    setTimeout(() => {
      modal.classList.add('hidden');
      document.body.style.overflow = 'auto';
      idEmpresaActual = null;
      // Limpiar campo _method para la próxima vez
      document.getElementById('campoMetodoEmpresa').innerHTML = '';
    }, 300);
  }

  // Cerrar modal cuando se presiona la tecla "Escape"
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && idEmpresaActual !== null) {
      cerrarModalEmpresa();
    }
  });
</script>
