<!-- ==============================
     Modal para editar Coordinador
   ============================== -->
<div
  id="modalEditarCoordinador"
  class="fixed inset-0 w-screen h-screen bg-black/50 hidden z-50 opacity-0 transition-opacity duration-300"
  role="dialog"
  aria-modal="true"
  aria-labelledby="tituloModalEditarCoordinador"
>
  <!-- Capa semitransparente (clic aquí cierra modal) -->
  <div class="absolute inset-0" onclick="cerrarModalEditarCoordinador()"></div>

  <!-- Contenedor centrado -->
  <div class="fixed inset-0 flex items-center justify-center p-4">
    <div
      id="contenidoModalEditarCoordinador"
      class="bg-white rounded-2xl shadow-xl w-full max-w-lg flex flex-col overflow-hidden min-h-0 transform scale-95 transition-transform duration-300"
    >
      <form
        id="formularioEditarCoordinador"
        method="POST"
        enctype="multipart/form-data"
        class="flex flex-col h-full"
      >
        @csrf
        <div id="campoMetodoCoordinador"></div>
        <!-- action se asignará dinámicamente en JS -->
        
        <!-- Cabecera fija -->
        <header
          class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center flex-shrink-0"
        >
          <h3 id="tituloModalEditarCoordinador" class="text-lg font-semibold text-gray-900">
            Editar Coordinador
          </h3>
          <button
            type="button"
            onclick="cerrarModalEditarCoordinador()"
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
              for="inputNombresEditar"
              class="block mb-1 text-sm font-medium text-gray-700"
            >
              Nombres
            </label>
            <input
              type="text"
              name="nombres"
              id="inputNombresEditar"
              required
              class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <!-- Apellido Paterno -->
          <div>
            <label
              for="inputApellidoPaternoEditar"
              class="block mb-1 text-sm font-medium text-gray-700"
            >
              Apellido Paterno
            </label>
            <input
              type="text"
              name="apellido_paterno"
              id="inputApellidoPaternoEditar"
              required
              class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <!-- Apellido Materno -->
          <div>
            <label
              for="inputApellidoMaternoEditar"
              class="block mb-1 text-sm font-medium text-gray-700"
            >
              Apellido Materno
            </label>
            <input
              type="text"
              name="apellido_materno"
              id="inputApellidoMaternoEditar"
              required
              class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <!-- Correo (readonly) -->
          <div>
            <label
              for="inputCorreoEditar"
              class="block mb-1 text-sm font-medium text-gray-700"
            >
              Correo Corporativo
            </label>
            <input
              type="email"
              name="correo"
              id="inputCorreoEditar"
              readonly
              class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <!-- Contraseña (readonly) -->
          {{-- <div>
            <label
              for="inputPasswordEditar"
              class="block mb-1 text-sm font-medium text-gray-700"
            >
              Contraseña (solo lectura)
            </label>
            <input
              type="text"
              name="password"
              id="inputPasswordEditar"
              readonly
              class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div> --}}

          <!-- Documento de Identidad -->
          <div>
            <label
              for="inputDocumentoEditar"
              class="block mb-1 text-sm font-medium text-gray-700"
            >
              Documento de Identidad
            </label>
            <input
              type="text"
              name="doc_identidad"
              id="inputDocumentoEditar"
              class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <!-- Fecha de Nacimiento -->
          <div>
            <label
              for="inputNacimientoEditar"
              class="block mb-1 text-sm font-medium text-gray-700"
            >
              Fecha de Nacimiento
            </label>
            <input
              type="date"
              name="fecha_nacimiento"
              id="inputNacimientoEditar"
              class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <!-- Teléfono -->
          <div>
            <label
              for="inputTelefonoEditar"
              class="block mb-1 text-sm font-medium text-gray-700"
            >
              Teléfono
            </label>
            <input
              type="tel"
              name="telefono"
              id="inputTelefonoEditar"
              class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <!-- Foto de Perfil con vista previa -->
          <div>
            <label
              for="avatarEditar"
              class="block mb-1 text-sm font-medium text-gray-700"
            >
              Foto de Perfil
            </label>
            <div class="flex items-center space-x-4">
              <div class="w-18 h-18 rounded-full overflow-hidden border-4 border-gray-300 bg-gray-100 flex items-center justify-center relative">
                <img
                  id="vistaPreviaAvatarEditar"
                  src=""
                  alt="Vista previa del avatar"
                  class="w-full h-full object-cover absolute top-0 left-0 hidden rounded-full"
                />
              </div>
              <div>
                <input
                  type="file"
                  name="avatar"
                  id="avatarEditar"
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
            onclick="cerrarModalEditarCoordinador()"
            class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            Cancelar
          </button>
          <button
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          >
            Guardar Cambios
          </button>
        </footer>
      </form>
    </div>
  </div>
</div>

<script>
  let idCoordinadorActual = null;

  async function abrirModalEditarCoordinador(id) {
    idCoordinadorActual = id;

    const formulario = document.getElementById('formularioEditarCoordinador');
    formulario.action = `/admin/coordinadores-equipos/${id}`;
    // Insertar campo _method para PUT
    document.getElementById('campoMetodoCoordinador').innerHTML =
      `<input type="hidden" name="_method" value="PUT">`;

    try {
      const respuesta = await fetch(`/admin/coordinadores-equipos/${id}`);
      if (!respuesta.ok) throw new Error('No se recibieron datos del colaborador');
      const data = await respuesta.json();

      // Rellenar campos con los datos recibidos
      document.getElementById('inputNombresEditar').value = data.nombres ?? '';
      document.getElementById('inputApellidoPaternoEditar').value = data.apellido_paterno ?? '';
      document.getElementById('inputApellidoMaternoEditar').value = data.apellido_materno ?? '';
      document.getElementById('inputCorreoEditar').value = data.correo ?? '';
    //   document.getElementById('inputPasswordEditar').value = data.password_mostrar ?? ''; 
        // “password_mostrar” sería la contraseña que guardas para mostrar, si existe
      document.getElementById('inputDocumentoEditar').value = data.doc_identidad ?? '';
      document.getElementById('inputNacimientoEditar').value = data.fecha_nacimiento ?? '';
      document.getElementById('inputTelefonoEditar').value = data.telefono ?? '';

      // Si existe una URL de avatar, mostrar la vista previa
      const imgPreview = document.getElementById('vistaPreviaAvatarEditar');
      if (data.avatar_url) {
        imgPreview.src = data.avatar_url;
        imgPreview.classList.remove('hidden');
      } else {
        imgPreview.classList.add('hidden');
      }

      // Mostrar modal con animación
      const modal = document.getElementById('modalEditarCoordinador');
      const contenido = document.getElementById('contenidoModalEditarCoordinador');
      modal.classList.remove('hidden');
      setTimeout(() => {
        modal.classList.remove('opacity-0');
        contenido.classList.remove('scale-95');
      }, 10);
      document.body.style.overflow = 'hidden';
    }
    catch (error) {
      console.error(error);
      alert('Error al cargar los datos del colaborador. Revisa la consola para más detalles.');
    }
  }

  /**
   * Cierra el modal de edición del colaborador con animación inversa.
   */
  function cerrarModalEditarCoordinador() {
    const modal = document.getElementById('modalEditarCoordinador');
    const contenido = document.getElementById('contenidoModalEditarCoordinador');

    modal.classList.add('opacity-0');
    contenido.classList.add('scale-95');

    setTimeout(() => {
      modal.classList.add('hidden');
      document.body.style.overflow = 'auto';
      idCoordinadorActual = null;
      // Limpiar campo _method para la próxima vez
      document.getElementById('campoMetodoCoordinador').innerHTML = '';
      // Limpiar preview de imagen
      document.getElementById('vistaPreviaAvatarEditar').classList.add('hidden');
    }, 300);
  }

  // Listener para actualizar la vista previa de la foto cuando seleccionan un archivo nuevo
  document.getElementById('avatarEditar').addEventListener('change', function (e) {
    const archivo = e.target.files[0];
    const imgPreview = document.getElementById('vistaPreviaAvatarEditar');
    if (archivo) {
      const lector = new FileReader();
      lector.onload = () => {
        imgPreview.src = lector.result;
        imgPreview.classList.remove('hidden');
      };
      lector.readAsDataURL(archivo);
    }
  });

  // Cerrar modal con tecla Escape
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && idCoordinadorActual !== null) {
      cerrarModalEditarCoordinador();
    }
  });
</script>
