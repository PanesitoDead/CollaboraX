<div
  id="modalEditarColaborador"
  class="fixed inset-0 w-screen h-screen bg-black/50 hidden z-50 opacity-0 transition-opacity duration-300"
  role="dialog"
  aria-modal="true"
  aria-labelledby="tituloModalEditarColaborador"
>
  <!-- Capa semitransparente (clic aquí cierra modal) -->
  <div class="absolute inset-0" onclick="cerrarModalEditarColaborador()"></div>

  <!-- Contenedor centrado -->
  <div class="fixed inset-0 flex items-center justify-center p-4">
    <div
      id="contenidoModalEditarColaborador"
      class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] flex flex-col overflow-hidden transform scale-95 transition-transform duration-300"
      style="height: fit-content; min-height: 300px;"
    >
      <form
        id="formularioEditarColaborador"
        method="POST"
        enctype="multipart/form-data"
        class="flex flex-col h-full"
      >
        @csrf
        <div id="campoMetodoColaborador"></div>
        <!-- action se asignará dinámicamente en JS -->
        
        <!-- Cabecera fija -->
        <header
          class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center flex-shrink-0"
        >
          <h3 id="tituloModalEditarColaborador" class="text-lg font-semibold text-gray-900">
            Editar Colaborador
          </h3>
          <button
            type="button"
            onclick="cerrarModalEditarColaborador()"
            aria-label="Cerrar modal"
            class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
          >
            <i data-lucide="x" class="w-6 h-6 text-gray-500"></i>
          </button>
        </header>

        <!-- Contenido scrollable -->
        <div
          class="px-6 py-4 space-y-4 overflow-y-auto flex-grow"
          style="max-height: calc(90vh - 140px);"
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
            <div id="errorNombresEditar" class="text-red-500 text-xs mt-1 hidden"></div>
            <div id="successNombresEditar" class="text-green-500 text-xs mt-1 hidden"></div>
          </div>
          
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
              <div id="errorApellidoPaternoEditar" class="text-red-500 text-xs mt-1 hidden"></div>
              <div id="successApellidoPaternoEditar" class="text-green-500 text-xs mt-1 hidden"></div>
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
              <div id="errorApellidoMaternoEditar" class="text-red-500 text-xs mt-1 hidden"></div>
              <div id="successApellidoMaternoEditar" class="text-green-500 text-xs mt-1 hidden"></div>
            </div>
          </div>
          <!-- Correo Personal -->
          <div>
            <label
              for="inputCorreoPersonalEditar"
              class="block mb-1 text-sm font-medium text-gray-700"
            >
              Correo Personal
            </label>
            <input
              type="email"
              name="correo_personal"
              id="inputCorreoPersonalEditar"
              class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
            <div id="errorCorreoPersonalEditar" class="text-red-500 text-xs mt-1 hidden"></div>
            <div id="successCorreoPersonalEditar" class="text-green-500 text-xs mt-1 hidden"></div>
          </div>
          <!-- Correo Corporativo (readonly) -->
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
          <div>
            <label
              for="inputPasswordEditar"
              class="block mb-1 text-sm font-medium text-gray-700"
            >
              Contraseña
            </label>
            <div class="flex items-center space-x-2">
              <input
                type="text"
                name="clave"
                id="inputPasswordEditar"
                readonly
                class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
              <!-- Botón copiar -->
              <button
                type="button"
                onclick="copiarClaveMostrar()"
                class="mt-1 p-2.5 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition"
                title="Copiar contraseña"
              >
                <i data-lucide="clipboard" class="w-5 h-5 text-gray-600"></i>
              </button>
            </div>
          </div>

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
            <div id="errorDocumentoEditar" class="text-red-500 text-xs mt-1 hidden"></div>
            <div id="successDocumentoEditar" class="text-green-500 text-xs mt-1 hidden"></div>
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
            <div id="errorTelefonoEditar" class="text-red-500 text-xs mt-1 hidden"></div>
            <div id="successTelefonoEditar" class="text-green-500 text-xs mt-1 hidden"></div>
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
            <div id="errorNacimientoEditar" class="text-red-500 text-xs mt-1 hidden"></div>
            <div id="successNacimientoEditar" class="text-green-500 text-xs mt-1 hidden"></div>
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
            onclick="cerrarModalEditarColaborador()"
            class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            Cancelar
          </button>
          <button
            type="submit"
            id="btnActualizarColaborador"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed"
          >
            <span id="btnTextoEditar">Guardar Cambios</span>
            <span id="btnLoadingEditar" class="hidden">
              <i class="fas fa-spinner fa-spin mr-2"></i>Validando...
            </span>
          </button>
        </footer>
      </form>
    </div>
  </div>
</div>

<script>
  let idColaboradorActual = null;
  
  // Variables para debounce
  let timeoutNombresEditar = null;
  let timeoutApellidoPaternoEditar = null;
  let timeoutApellidoMaternoEditar = null;
  let timeoutCorreoPersonalEditar = null;
  let timeoutDocumentoEditar = null;
  let timeoutTelefonoEditar = null;
  let timeoutNacimientoEditar = null;

  // Variable para rastrear el estado de validación de cada campo en edición
  let estadosValidacionColaboradorEditar = {
    nombres: null,           // null = no validado, true = válido, false = inválido
    apellidoPaterno: null,
    apellidoMaterno: null,
    correoPersonal: null,
    documento: true,         // opcional
    telefono: true,          // opcional
    nacimiento: true         // opcional
  };

  // Función para verificar si el formulario de edición es válido
  function formularioColaboradorEditarEsValido() {
    // Verificar que todos los campos requeridos estén validados y sean válidos
    return estadosValidacionColaboradorEditar.nombres === true && 
           estadosValidacionColaboradorEditar.apellidoPaterno === true && 
           estadosValidacionColaboradorEditar.apellidoMaterno === true &&
           estadosValidacionColaboradorEditar.correoPersonal === true;
    // Los campos opcionales no afectan la validez si están en null o true
  }

  // Función para actualizar el estado del botón de envío de edición
  function actualizarBotonEnvioColaboradorEditar() {
    const btnEnviar = document.getElementById('btnActualizarColaborador');
    if (!btnEnviar) return;
    
    if (formularioColaboradorEditarEsValido()) {
      btnEnviar.disabled = false;
      btnEnviar.classList.remove('bg-gray-400', 'cursor-not-allowed');
      btnEnviar.classList.add('bg-blue-600', 'hover:bg-blue-700');
    } else {
      btnEnviar.disabled = true;
      btnEnviar.classList.remove('bg-blue-600', 'hover:bg-blue-700');
      btnEnviar.classList.add('bg-gray-400', 'cursor-not-allowed');
    }
  }

  async function abrirModalEditarColaborador(id) {
    idColaboradorActual = id;

    const formulario = document.getElementById('formularioEditarColaborador');
    formulario.action = `/admin/colaboradores/${id}`;
    // Insertar campo _method para PUT
    document.getElementById('campoMetodoColaborador').innerHTML =
      `<input type="hidden" name="_method" value="PUT">`;

    try {
      const respuesta = await fetch(`/admin/colaboradores/${id}`);
      if (!respuesta.ok) throw new Error('No se recibieron datos del colaborador');
      const data = await respuesta.json();

      // Limpiar mensajes y estilos previos
      limpiarCamposYMensajesEditar();

      // Rellenar campos con los datos recibidos
      document.getElementById('inputNombresEditar').value = data.nombres ?? '';
      document.getElementById('inputApellidoPaternoEditar').value = data.apellido_paterno ?? '';
      document.getElementById('inputApellidoMaternoEditar').value = data.apellido_materno ?? '';
      document.getElementById('inputCorreoPersonalEditar').value = data.correo_personal ?? '';
      document.getElementById('inputCorreoEditar').value = data.correo ?? '';
      document.getElementById('inputPasswordEditar').value = data.clave_mostrar ?? ''; 
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

      // Validar campos prellenados y actualizar estados
      setTimeout(() => {
        // Validar campos requeridos que vienen prellenados
        if (data.nombres) {
          estadosValidacionColaboradorEditar.nombres = true;
        }
        if (data.apellido_paterno) {
          estadosValidacionColaboradorEditar.apellidoPaterno = true;
        }
        if (data.apellido_materno) {
          estadosValidacionColaboradorEditar.apellidoMaterno = true;
        }
        if (data.correo_personal) {
          estadosValidacionColaboradorEditar.correoPersonal = true;
        }
        
        // Los campos opcionales ya están en true por defecto
        actualizarBotonEnvioColaboradorEditar();
      }, 100);

      // Mostrar modal con animación
      const modal = document.getElementById('modalEditarColaborador');
      const contenido = document.getElementById('contenidoModalEditarColaborador');
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

  function copiarClaveMostrar() {
    const inputPass = document.getElementById('inputPasswordEditar');
    inputPass.select();
    inputPass.setSelectionRange(0, 99999); // Para móviles
    document.execCommand('copy');
    // Opcional: mostrar un breve tooltip o mensaje
    alert('Contraseña copiada al portapapeles.');
  }

  /**
   * Limpia todos los campos y mensajes del modal de edición
   */
  function limpiarCamposYMensajesEditar() {
    // Limpiar mensajes de error y éxito
    const mensajes = ['NombresEditar', 'ApellidoPaternoEditar', 'ApellidoMaternoEditar', 'CorreoPersonalEditar', 
                      'DocumentoEditar', 'TelefonoEditar', 'NacimientoEditar'];
    mensajes.forEach(campo => {
      const errorElement = document.getElementById(`error${campo}`);
      const successElement = document.getElementById(`success${campo}`);
      if (errorElement) errorElement.classList.add('hidden');
      if (successElement) successElement.classList.add('hidden');
    });

    // Resetear estilos de los inputs
    const inputs = document.querySelectorAll('#modalEditarColaborador input[type="text"], #modalEditarColaborador input[type="email"], #modalEditarColaborador input[type="tel"], #modalEditarColaborador input[type="date"]');
    inputs.forEach(input => {
      input.classList.remove('border-red-500', 'border-green-500');
      input.classList.add('border-gray-300');
    });

    // Resetear estados de validación
    estadosValidacionColaboradorEditar = {
      nombres: null,
      apellidoPaterno: null,
      apellidoMaterno: null,
      correoPersonal: null,
      documento: true,     // opcional
      telefono: true,      // opcional
      nacimiento: true     // opcional
    };

    // Actualizar botón
    actualizarBotonEnvioColaboradorEditar();
  }

  /**
   * Muestra mensaje de error para edición
   */
  function mostrarErrorEditar(campo, mensaje) {
    const errorElement = document.getElementById(`error${campo}`);
    const successElement = document.getElementById(`success${campo}`);
    
    // Mapear nombres de campo para encontrar los inputs correctos y estados de validación
    let inputId = '';
    let campoValidacion = '';
    switch (campo) {
      case 'NombresEditar':
        inputId = 'inputNombresEditar';
        campoValidacion = 'nombres';
        break;
      case 'ApellidoPaternoEditar':
        inputId = 'inputApellidoPaternoEditar';
        campoValidacion = 'apellidoPaterno';
        break;
      case 'ApellidoMaternoEditar':
        inputId = 'inputApellidoMaternoEditar';
        campoValidacion = 'apellidoMaterno';
        break;
      case 'CorreoPersonalEditar':
        inputId = 'inputCorreoPersonalEditar';
        campoValidacion = 'correoPersonal';
        break;
      case 'DocumentoEditar':
        inputId = 'inputDocumentoEditar';
        campoValidacion = 'documento';
        break;
      case 'TelefonoEditar':
        inputId = 'inputTelefonoEditar';
        campoValidacion = 'telefono';
        break;
      case 'NacimientoEditar':
        inputId = 'inputNacimientoEditar';
        campoValidacion = 'nacimiento';
        break;
    }
    
    const inputElement = document.getElementById(inputId);

    if (errorElement) {
      errorElement.textContent = mensaje;
      errorElement.classList.remove('hidden');
    }
    if (successElement) {
      successElement.classList.add('hidden');
    }
    
    if (inputElement) {
      inputElement.classList.remove('border-gray-300', 'border-green-500');
      inputElement.classList.add('border-red-500');
    }

    // Actualizar estado de validación
    if (campoValidacion) {
      estadosValidacionColaboradorEditar[campoValidacion] = false;
      actualizarBotonEnvioColaboradorEditar();
    }
  }

  /**
   * Muestra mensaje de éxito para edición
   */
  function mostrarExitoEditar(campo, mensaje) {
    const errorElement = document.getElementById(`error${campo}`);
    const successElement = document.getElementById(`success${campo}`);
    
    // Mapear nombres de campo para encontrar los inputs correctos y estados de validación
    let inputId = '';
    let campoValidacion = '';
    switch (campo) {
      case 'NombresEditar':
        inputId = 'inputNombresEditar';
        campoValidacion = 'nombres';
        break;
      case 'ApellidoPaternoEditar':
        inputId = 'inputApellidoPaternoEditar';
        campoValidacion = 'apellidoPaterno';
        break;
      case 'ApellidoMaternoEditar':
        inputId = 'inputApellidoMaternoEditar';
        campoValidacion = 'apellidoMaterno';
        break;
      case 'CorreoPersonalEditar':
        inputId = 'inputCorreoPersonalEditar';
        campoValidacion = 'correoPersonal';
        break;
      case 'DocumentoEditar':
        inputId = 'inputDocumentoEditar';
        campoValidacion = 'documento';
        break;
      case 'TelefonoEditar':
        inputId = 'inputTelefonoEditar';
        campoValidacion = 'telefono';
        break;
      case 'NacimientoEditar':
        inputId = 'inputNacimientoEditar';
        campoValidacion = 'nacimiento';
        break;
    }
    
    const inputElement = document.getElementById(inputId);

    if (successElement) {
      successElement.textContent = mensaje;
      successElement.classList.remove('hidden');
    }
    if (errorElement) {
      errorElement.classList.add('hidden');
    }
    
    if (inputElement) {
      inputElement.classList.remove('border-gray-300', 'border-red-500');
      inputElement.classList.add('border-green-500');
    }

    // Actualizar estado de validación
    if (campoValidacion) {
      estadosValidacionColaboradorEditar[campoValidacion] = true;
      actualizarBotonEnvioColaboradorEditar();
    }
  }

  /**
   * Oculta todos los mensajes de un campo para edición
   */
  function ocultarMensajesEditar(campo) {
    const errorElement = document.getElementById(`error${campo}`);
    const successElement = document.getElementById(`success${campo}`);
    
    // Mapear nombres de campo para encontrar los inputs correctos y estados de validación
    let inputId = '';
    let campoValidacion = '';
    switch (campo) {
      case 'NombresEditar':
        inputId = 'inputNombresEditar';
        campoValidacion = 'nombres';
        break;
      case 'ApellidoPaternoEditar':
        inputId = 'inputApellidoPaternoEditar';
        campoValidacion = 'apellidoPaterno';
        break;
      case 'ApellidoMaternoEditar':
        inputId = 'inputApellidoMaternoEditar';
        campoValidacion = 'apellidoMaterno';
        break;
      case 'CorreoPersonalEditar':
        inputId = 'inputCorreoPersonalEditar';
        campoValidacion = 'correoPersonal';
        break;
      case 'DocumentoEditar':
        inputId = 'inputDocumentoEditar';
        campoValidacion = 'documento';
        break;
      case 'TelefonoEditar':
        inputId = 'inputTelefonoEditar';
        campoValidacion = 'telefono';
        break;
      case 'NacimientoEditar':
        inputId = 'inputNacimientoEditar';
        campoValidacion = 'nacimiento';
        break;
    }
    
    const inputElement = document.getElementById(inputId);

    if (errorElement) {
      errorElement.classList.add('hidden');
    }
    if (successElement) {
      successElement.classList.add('hidden');
    }
    
    if (inputElement) {
      inputElement.classList.remove('border-red-500', 'border-green-500');
      inputElement.classList.add('border-gray-300');
    }

    // Resetear estado de validación cuando se ocultan mensajes
    if (campoValidacion) {
      // Para campos opcionales, resetear a true; para requeridos, a null
      if (['documento', 'telefono', 'nacimiento'].includes(campoValidacion)) {
        estadosValidacionColaboradorEditar[campoValidacion] = true;
      } else {
        estadosValidacionColaboradorEditar[campoValidacion] = null;
      }
      actualizarBotonEnvioColaboradorEditar();
    }
  }

  /**
   * Valida un campo mediante AJAX para edición
   */
  function validarCampoEditar(campo, valor) {
    if (!valor.trim()) {
      ocultarMensajesEditar(campo);
      return;
    }

    // Mapear los nombres de campos del frontend a los del backend
    let campoBackend = campo.toLowerCase().replace('editar', '');
    switch (campo) {
      case 'NombresEditar':
        campoBackend = 'nombres';
        break;
      case 'ApellidoPaternoEditar':
        campoBackend = 'apellido_paterno';
        break;
      case 'ApellidoMaternoEditar':
        campoBackend = 'apellido_materno';
        break;
      case 'DocumentoEditar':
        campoBackend = 'doc_identidad';
        break;
      case 'TelefonoEditar':
        campoBackend = 'telefono';
        break;
      case 'NacimientoEditar':
        campoBackend = 'fecha_nacimiento';
        break;
    }

    console.log('Validando campo edición:', campo, 'Backend:', campoBackend, 'Valor:', valor);

    fetch('{{ route("admin.colaboradores.validar-campo-edicion") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({
        campo: campoBackend,
        valor: valor
      })
    })
    .then(response => response.json())
    .then(data => {
      console.log('Respuesta del servidor edición:', data);
      if (data.valido) {
        mostrarExitoEditar(campo, data.mensaje);
      } else {
        mostrarErrorEditar(campo, data.mensaje);
      }
    })
    .catch(error => {
      console.error('Error en la validación de edición:', error);
      mostrarErrorEditar(campo, 'Error de conexión al validar el campo.');
    });
  }

  /**
   * Valida el correo personal para edición
   */
  function validarCorreoPersonalEditar() {
    const correoPersonal = document.getElementById('inputCorreoPersonalEditar').value.trim();
    
    if (!correoPersonal) {
      ocultarMensajesEditar('CorreoPersonalEditar');
      return;
    }

    fetch('{{ route("admin.colaboradores.validar-correo-personal-edicion") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({
        correo_personal: correoPersonal,
        colaborador_id: idColaboradorActual
      })
    })
    .then(response => response.json())
    .then(data => {
      if (data.valido) {
        mostrarExitoEditar('CorreoPersonalEditar', data.mensaje);
      } else {
        mostrarErrorEditar('CorreoPersonalEditar', data.mensaje);
      }
    })
    .catch(error => {
      console.error('Error en la validación del correo personal para edición:', error);
    });
  }

  /**
   * Valida que todos los campos del formulario de edición sean válidos
   */
  function validarFormularioCompletoEditar() {
    const nombres = document.getElementById('inputNombresEditar').value.trim();
    const apellidoPaterno = document.getElementById('inputApellidoPaternoEditar').value.trim();
    const apellidoMaterno = document.getElementById('inputApellidoMaternoEditar').value.trim();
    const correoPersonal = document.getElementById('inputCorreoPersonalEditar').value.trim();

    // Verificar que todos los campos requeridos estén llenos
    if (!nombres || !apellidoPaterno || !apellidoMaterno || !correoPersonal) {
      return false;
    }

    // Verificar que no haya mensajes de error visibles
    const errores = document.querySelectorAll('#modalEditarColaborador [id^="error"]:not(.hidden)');
    return errores.length === 0;
  }

  /**
   * Cierra el modal de edición del colaborador con animación inversa.
   */
  function cerrarModalEditarColaborador() {
    const modal = document.getElementById('modalEditarColaborador');
    const contenido = document.getElementById('contenidoModalEditarColaborador');

    modal.classList.add('opacity-0');
    contenido.classList.add('scale-95');

    setTimeout(() => {
      modal.classList.add('hidden');
      document.body.style.overflow = 'auto';
      idColaboradorActual = null;
      // Limpiar campo _method para la próxima vez
      document.getElementById('campoMetodoColaborador').innerHTML = '';
      // Limpiar preview de imagen
      document.getElementById('vistaPreviaAvatarEditar').classList.add('hidden');
    }, 300);
  }

  // Event listeners para validaciones en tiempo real
  document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up edit validators');
    
    // Validación de nombres
    const inputNombresEditar = document.getElementById('inputNombresEditar');
    if (inputNombresEditar) {
      inputNombresEditar.addEventListener('input', function() {
        clearTimeout(timeoutNombresEditar);
        timeoutNombresEditar = setTimeout(() => {
          validarCampoEditar('NombresEditar', this.value);
        }, 500);
      });
    }

    // Validación de apellido paterno
    const inputApellidoPaternoEditar = document.getElementById('inputApellidoPaternoEditar');
    if (inputApellidoPaternoEditar) {
      inputApellidoPaternoEditar.addEventListener('input', function() {
        clearTimeout(timeoutApellidoPaternoEditar);
        timeoutApellidoPaternoEditar = setTimeout(() => {
          validarCampoEditar('ApellidoPaternoEditar', this.value);
        }, 500);
      });
    }

    // Validación de apellido materno
    const inputApellidoMaternoEditar = document.getElementById('inputApellidoMaternoEditar');
    if (inputApellidoMaternoEditar) {
      inputApellidoMaternoEditar.addEventListener('input', function() {
        clearTimeout(timeoutApellidoMaternoEditar);
        timeoutApellidoMaternoEditar = setTimeout(() => {
          validarCampoEditar('ApellidoMaternoEditar', this.value);
        }, 500);
      });
    }

    // Validación de correo personal
    const inputCorreoPersonalEditar = document.getElementById('inputCorreoPersonalEditar');
    if (inputCorreoPersonalEditar) {
      inputCorreoPersonalEditar.addEventListener('input', function() {
        clearTimeout(timeoutCorreoPersonalEditar);
        timeoutCorreoPersonalEditar = setTimeout(() => {
          validarCorreoPersonalEditar();
        }, 800);
      });
    }

    // Validación de documento
    const inputDocumentoEditar = document.getElementById('inputDocumentoEditar');
    if (inputDocumentoEditar) {
      inputDocumentoEditar.addEventListener('input', function() {
        clearTimeout(timeoutDocumentoEditar);
        timeoutDocumentoEditar = setTimeout(() => {
          validarCampoEditar('DocumentoEditar', this.value);
        }, 500);
      });
    }

    // Validación de teléfono
    const inputTelefonoEditar = document.getElementById('inputTelefonoEditar');
    if (inputTelefonoEditar) {
      inputTelefonoEditar.addEventListener('input', function() {
        clearTimeout(timeoutTelefonoEditar);
        timeoutTelefonoEditar = setTimeout(() => {
          validarCampoEditar('TelefonoEditar', this.value);
        }, 500);
      });
    }

    // Validación de fecha de nacimiento
    const inputNacimientoEditar = document.getElementById('inputNacimientoEditar');
    if (inputNacimientoEditar) {
      inputNacimientoEditar.addEventListener('change', function() {
        validarCampoEditar('NacimientoEditar', this.value);
      });
    }

    // Manejo del envío del formulario de edición
    const formularioEditar = document.getElementById('formularioEditarColaborador');
    if (formularioEditar) {
      formularioEditar.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Verificar si el formulario es válido antes de enviar
        if (!formularioColaboradorEditarEsValido()) {
          Swal.fire({
            icon: 'warning',
            title: 'Formulario incompleto',
            text: 'Por favor, complete todos los campos requeridos correctamente antes de guardar.',
            showConfirmButton: true,
            timer: 5000
          });
          return false;
        }
        
        const btnSubmit = document.getElementById('btnActualizarColaborador');
        const btnTexto = document.getElementById('btnTextoEditar');
        const btnLoading = document.getElementById('btnLoadingEditar');
        
        // Deshabilitar botón y mostrar loading
        if (btnSubmit) btnSubmit.disabled = true;
        if (btnTexto) btnTexto.classList.add('hidden');
        if (btnLoading) btnLoading.classList.remove('hidden');
        
        // Validar todos los campos antes de enviar
        if (validarFormularioCompletoEditar()) {
          this.submit();
        } else {
          // Rehabilitar botón si hay errores
          if (btnSubmit) btnSubmit.disabled = false;
          if (btnTexto) btnTexto.classList.remove('hidden');
          if (btnLoading) btnLoading.classList.add('hidden');
          
          alert('Por favor, corrija los errores en el formulario antes de continuar.');
        }
      });
    }
  });

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
    if (e.key === 'Escape' && idColaboradorActual !== null) {
      cerrarModalEditarColaborador();
    }
  });
</script>
