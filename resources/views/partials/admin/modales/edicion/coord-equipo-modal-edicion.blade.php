<div
  id="modalEditarCoordinador"
  class="fixed inset-0 w-screen h-screen bg-black/50 hidden z-50 opacity-0 transition-opacity duration-300"
  role="dialog"
  aria-modal="true"
  aria-labelledby="tituloModalEditarCoordinador"
  data-coordinador-id=""
>
  <!-- Capa semitransparente (clic aquí cierra modal) -->
  <div class="absolute inset-0" onclick="cerrarModalEditarCoordinador()"></div>

  <!-- Contenedor centrado -->
  <div class="fixed inset-0 flex items-center justify-center p-4">
    <div
      id="contenidoModalEditarCoordinador"
      class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] flex flex-col overflow-hidden transform scale-95 transition-transform duration-300"
      style="height: fit-content; min-height: 300px;"
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
            <div id="errorNombresEditarCoord" class="text-red-500 text-xs mt-1 hidden"></div>
            <div id="successNombresEditarCoord" class="text-green-500 text-xs mt-1 hidden"></div>
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
            <div id="errorApellidoPaternoEditarCoord" class="text-red-500 text-xs mt-1 hidden"></div>
            <div id="successApellidoPaternoEditarCoord" class="text-green-500 text-xs mt-1 hidden"></div>
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
            <div id="errorApellidoMaternoEditarCoord" class="text-red-500 text-xs mt-1 hidden"></div>
            <div id="successApellidoMaternoEditarCoord" class="text-green-500 text-xs mt-1 hidden"></div>
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
            <div id="errorCorreoPersonalEditarCoord" class="text-red-500 text-xs mt-1 hidden"></div>
            <div id="successCorreoPersonalEditarCoord" class="text-green-500 text-xs mt-1 hidden"></div>
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
            <div id="errorDocumentoEditarCoord" class="text-red-500 text-xs mt-1 hidden"></div>
            <div id="successDocumentoEditarCoord" class="text-green-500 text-xs mt-1 hidden"></div>
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
            <div id="errorNacimientoEditarCoord" class="text-red-500 text-xs mt-1 hidden"></div>
            <div id="successNacimientoEditarCoord" class="text-green-500 text-xs mt-1 hidden"></div>
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
            <div id="errorTelefonoEditarCoord" class="text-red-500 text-xs mt-1 hidden"></div>
            <div id="successTelefonoEditarCoord" class="text-green-500 text-xs mt-1 hidden"></div>
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
            id="btnActualizarCoordinador"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed"
          >
            <span id="btnTextoEditarCoord">Guardar Cambios</span>
            <span id="btnLoadingEditarCoord" class="hidden">
              <i class="fas fa-spinner fa-spin mr-2"></i>Validando...
            </span>
          </button>
        </footer>
      </form>
    </div>
  </div>
</div>

<script>
  let idCoordinadorActual = null;
  
  // Variables para debounce
  let timeoutNombresEditarCoord = null;
  let timeoutApellidoPaternoEditarCoord = null;
  let timeoutApellidoMaternoEditarCoord = null;
  let timeoutCorreoPersonalEditarCoord = null;
  let timeoutDocumentoEditarCoord = null;
  let timeoutTelefonoEditarCoord = null;
  let timeoutNacimientoEditarCoord = null;

  // Estados de validación para coordinador editar
  const estadosValidacionCoordinadorEditar = {
    nombres: null,           // Campo requerido
    apellidoPaterno: null,   // Campo requerido  
    apellidoMaterno: null,   // Campo requerido
    correoPersonal: null,    // Campo requerido
    documento: true,         // Campo opcional
    telefono: true,          // Campo opcional
    nacimiento: true         // Campo opcional
  };

  /**
   * Verifica si el formulario de coordinador editar es válido
   */
  function formularioCoordinadorEditarEsValido() {
    // Todos los campos requeridos deben ser true
    const camposRequeridos = ['nombres', 'apellidoPaterno', 'apellidoMaterno', 'correoPersonal'];
    return camposRequeridos.every(campo => estadosValidacionCoordinadorEditar[campo] === true);
  }

  /**
   * Actualiza el estado del botón de envío de edición de coordinador
   */
  function actualizarBotonEnvioCoordinadorEditar() {
    const boton = document.getElementById('btnActualizarCoordinador');
    if (!boton) return;
    
    const formularioValido = formularioCoordinadorEditarEsValido();
    
    if (formularioValido) {
      boton.disabled = false;
      boton.classList.remove('bg-gray-400', 'cursor-not-allowed');
      boton.classList.add('bg-blue-600', 'hover:bg-blue-700');
    } else {
      boton.disabled = true;
      boton.classList.remove('bg-blue-600', 'hover:bg-blue-700');
      boton.classList.add('bg-gray-400', 'cursor-not-allowed');
    }
  }

  async function abrirModalEditarCoordinador(id) {
    idCoordinadorActual = id;

    const formulario = document.getElementById('formularioEditarCoordinador');
    formulario.action = `/admin/coordinadores-equipos/${id}`;
    // Insertar campo _method para PUT
    document.getElementById('campoMetodoCoordinador').innerHTML =
      `<input type="hidden" name="_method" value="PUT">`;

    // Establecer el ID del coordinador en el modal para validaciones
    document.getElementById('modalEditarCoordinador').setAttribute('data-coordinador-id', id);

    try {
      const respuesta = await fetch(`/admin/coordinadores-equipos/${id}`);
      if (!respuesta.ok) throw new Error('No se recibieron datos del coordinador');
      const data = await respuesta.json();

      // Limpiar mensajes y estilos previos
      limpiarCamposYMensajesEditarCoord();

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
          estadosValidacionCoordinadorEditar.nombres = true;
        }
        if (data.apellido_paterno) {
          estadosValidacionCoordinadorEditar.apellidoPaterno = true;
        }
        if (data.apellido_materno) {
          estadosValidacionCoordinadorEditar.apellidoMaterno = true;
        }
        if (data.correo_personal) {
          estadosValidacionCoordinadorEditar.correoPersonal = true;
        }
        
        // Los campos opcionales ya están en true por defecto
        actualizarBotonEnvioCoordinadorEditar();
      }, 100);

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
  function limpiarCamposYMensajesEditarCoord() {
    // Limpiar mensajes de error y éxito
    const mensajes = ['NombresEditarCoord', 'ApellidoPaternoEditarCoord', 'ApellidoMaternoEditarCoord', 'CorreoPersonalEditarCoord', 
                      'DocumentoEditarCoord', 'TelefonoEditarCoord', 'NacimientoEditarCoord'];
    mensajes.forEach(campo => {
      const errorElement = document.getElementById(`error${campo}`);
      const successElement = document.getElementById(`success${campo}`);
      if (errorElement) errorElement.classList.add('hidden');
      if (successElement) successElement.classList.add('hidden');
    });

    // Resetear estilos de los inputs
    const inputs = document.querySelectorAll('#modalEditarCoordinador input[type="text"], #modalEditarCoordinador input[type="email"], #modalEditarCoordinador input[type="tel"], #modalEditarCoordinador input[type="date"]');
    inputs.forEach(input => {
      input.classList.remove('border-red-500', 'border-green-500');
      input.classList.add('border-gray-300');
    });

    // Resetear estados de validación
    estadosValidacionCoordinadorEditar.nombres = null;
    estadosValidacionCoordinadorEditar.apellidoPaterno = null;
    estadosValidacionCoordinadorEditar.apellidoMaterno = null;
    estadosValidacionCoordinadorEditar.correoPersonal = null;
    estadosValidacionCoordinadorEditar.documento = true;
    estadosValidacionCoordinadorEditar.telefono = true;
    estadosValidacionCoordinadorEditar.nacimiento = true;
    
    // Actualizar botón
    actualizarBotonEnvioCoordinadorEditar();
  }

  // Funciones de validación (simplificadas para coordinadores)
  function mostrarErrorEditarCoord(campo, mensaje) {
    console.log('🚨 Mostrando error para campo:', campo, 'Mensaje:', mensaje);
    
    const errorElement = document.getElementById(`error${campo}`);
    const successElement = document.getElementById(`success${campo}`);
    
    console.log('🔍 Elementos encontrados:', {
      error: errorElement ? 'Encontrado' : 'NO ENCONTRADO',
      success: successElement ? 'Encontrado' : 'NO ENCONTRADO'
    });
    
    // Mapear nombres de campo para encontrar los inputs correctos y estados de validación
    let inputId = '';
    let campoValidacion = '';
    switch (campo) {
      case 'NombresEditarCoord':
        inputId = 'inputNombresEditar';
        campoValidacion = 'nombres';
        break;
      case 'ApellidoPaternoEditarCoord':
        inputId = 'inputApellidoPaternoEditar';
        campoValidacion = 'apellidoPaterno';
        break;
      case 'ApellidoMaternoEditarCoord':
        inputId = 'inputApellidoMaternoEditar';
        campoValidacion = 'apellidoMaterno';
        break;
      case 'CorreoPersonalEditarCoord':
        inputId = 'inputCorreoPersonalEditar';
        campoValidacion = 'correoPersonal';
        break;
      case 'DocumentoEditarCoord':
        inputId = 'inputDocumentoEditar';
        campoValidacion = 'documento';
        break;
      case 'TelefonoEditarCoord':
        inputId = 'inputTelefonoEditar';
        campoValidacion = 'telefono';
        break;
      case 'NacimientoEditarCoord':
        inputId = 'inputNacimientoEditar';
        campoValidacion = 'nacimiento';
        break;
    }
    
    const inputElement = document.getElementById(inputId);
    console.log('📝 Input elemento:', inputElement ? 'Encontrado' : 'NO ENCONTRADO');
    
    if (errorElement) {
      errorElement.textContent = mensaje;
      errorElement.classList.remove('hidden');
    }
    if (successElement) successElement.classList.add('hidden');
    if (inputElement) {
      inputElement.classList.remove('border-gray-300', 'border-green-500');
      inputElement.classList.add('border-red-500');
    }

    // Actualizar estado de validación
    if (campoValidacion) {
      estadosValidacionCoordinadorEditar[campoValidacion] = false;
      actualizarBotonEnvioCoordinadorEditar();
    }
  }

  function mostrarExitoEditarCoord(campo, mensaje) {
    console.log('✅ Mostrando éxito para campo:', campo, 'Mensaje:', mensaje);
    
    const errorElement = document.getElementById(`error${campo}`);
    const successElement = document.getElementById(`success${campo}`);
    
    console.log('🔍 Elementos encontrados:', {
      error: errorElement ? 'Encontrado' : 'NO ENCONTRADO',
      success: successElement ? 'Encontrado' : 'NO ENCONTRADO'
    });
    
    // Mapear nombres de campo para encontrar los inputs correctos y estados de validación
    let inputId = '';
    let campoValidacion = '';
    switch (campo) {
      case 'NombresEditarCoord':
        inputId = 'inputNombresEditar';
        campoValidacion = 'nombres';
        break;
      case 'ApellidoPaternoEditarCoord':
        inputId = 'inputApellidoPaternoEditar';
        campoValidacion = 'apellidoPaterno';
        break;
      case 'ApellidoMaternoEditarCoord':
        inputId = 'inputApellidoMaternoEditar';
        campoValidacion = 'apellidoMaterno';
        break;
      case 'CorreoPersonalEditarCoord':
        inputId = 'inputCorreoPersonalEditar';
        campoValidacion = 'correoPersonal';
        break;
      case 'DocumentoEditarCoord':
        inputId = 'inputDocumentoEditar';
        campoValidacion = 'documento';
        break;
      case 'TelefonoEditarCoord':
        inputId = 'inputTelefonoEditar';
        campoValidacion = 'telefono';
        break;
      case 'NacimientoEditarCoord':
        inputId = 'inputNacimientoEditar';
        campoValidacion = 'nacimiento';
        break;
    }
    
    const inputElement = document.getElementById(inputId);
    console.log('📝 Input elemento:', inputElement ? 'Encontrado' : 'NO ENCONTRADO');
    
    if (successElement) {
      successElement.textContent = mensaje;
      successElement.classList.remove('hidden');
    }
    if (errorElement) errorElement.classList.add('hidden');
    if (inputElement) {
      inputElement.classList.remove('border-gray-300', 'border-red-500');
      inputElement.classList.add('border-green-500');
    }

    // Actualizar estado de validación
    if (campoValidacion) {
      estadosValidacionCoordinadorEditar[campoValidacion] = true;
      actualizarBotonEnvioCoordinadorEditar();
    }
  }

  function ocultarMensajesEditarCoord(campo) {
    const errorElement = document.getElementById(`error${campo}`);
    const successElement = document.getElementById(`success${campo}`);
    
    // Mapear nombres de campo para encontrar los inputs correctos y estados de validación
    let inputId = '';
    let campoValidacion = '';
    switch (campo) {
      case 'NombresEditarCoord':
        inputId = 'inputNombresEditar';
        campoValidacion = 'nombres';
        break;
      case 'ApellidoPaternoEditarCoord':
        inputId = 'inputApellidoPaternoEditar';
        campoValidacion = 'apellidoPaterno';
        break;
      case 'ApellidoMaternoEditarCoord':
        inputId = 'inputApellidoMaternoEditar';
        campoValidacion = 'apellidoMaterno';
        break;
      case 'CorreoPersonalEditarCoord':
        inputId = 'inputCorreoPersonalEditar';
        campoValidacion = 'correoPersonal';
        break;
      case 'DocumentoEditarCoord':
        inputId = 'inputDocumentoEditar';
        campoValidacion = 'documento';
        break;
      case 'TelefonoEditarCoord':
        inputId = 'inputTelefonoEditar';
        campoValidacion = 'telefono';
        break;
      case 'NacimientoEditarCoord':
        inputId = 'inputNacimientoEditar';
        campoValidacion = 'nacimiento';
        break;
    }
    
    const inputElement = document.getElementById(inputId);
    if (errorElement) errorElement.classList.add('hidden');
    if (successElement) successElement.classList.add('hidden');
    if (inputElement) {
      inputElement.classList.remove('border-red-500', 'border-green-500');
      inputElement.classList.add('border-gray-300');
    }

    // Resetear estado de validación cuando se ocultan mensajes
    if (campoValidacion) {
      // Para campos opcionales, resetear a true; para requeridos, a null
      if (['documento', 'telefono', 'nacimiento'].includes(campoValidacion)) {
        estadosValidacionCoordinadorEditar[campoValidacion] = true;
      } else {
        estadosValidacionCoordinadorEditar[campoValidacion] = null;
      }
      actualizarBotonEnvioCoordinadorEditar();
    }
  }

  // Variables para debounce
  let timeouts = {};

  // Función de debounce genérica
  function debounce(func, wait, key) {
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeouts[key]);
        delete timeouts[key];
        func(...args);
      };
      clearTimeout(timeouts[key]);
      timeouts[key] = setTimeout(later, wait);
    };
  }

  // Función para validar campos generales
  function validarCampoEditarCoord(campo, valor, coordinadorId = null) {
    console.log('🔍 Validando campo coordinador:', campo, 'Valor:', valor, 'ID:', coordinadorId);
    
    fetch('{{ route("validar.campo.coordinador.edicion") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({
        campo: campo,
        valor: valor,
        coordinador_id: coordinadorId
      })
    })
    .then(response => response.json())
    .then(data => {
      console.log('📥 Respuesta del servidor:', data);
      
      // Mapear correctamente los nombres de campo del backend al frontend
      let campoFrontend = '';
      switch (campo) {
        case 'nombres':
          campoFrontend = 'NombresEditarCoord';
          break;
        case 'apellido_paterno':
          campoFrontend = 'ApellidoPaternoEditarCoord';
          break;
        case 'apellido_materno':
          campoFrontend = 'ApellidoMaternoEditarCoord';
          break;
        case 'documento':
          campoFrontend = 'DocumentoEditarCoord';
          break;
        case 'telefono':
          campoFrontend = 'TelefonoEditarCoord';
          break;
        case 'fecha_nacimiento':
          campoFrontend = 'NacimientoEditarCoord';
          break;
        default:
          campoFrontend = campo.charAt(0).toUpperCase() + campo.slice(1) + 'EditarCoord';
      }
      
      console.log('🎯 Campo frontend mapeado:', campoFrontend);
      
      if (data.valido) {
        mostrarExitoEditarCoord(campoFrontend, data.mensaje);
      } else {
        mostrarErrorEditarCoord(campoFrontend, data.mensaje);
      }
    })
    .catch(error => {
      console.error('❌ Error en validación:', error);
    });
  }

  // Función específica para validar correo personal
  function validarCorreoPersonalEditarCoord(correo, coordinadorId = null) {
    fetch('{{ route("validar.correo.personal.coordinador.edicion") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({
        correo_personal: correo,
        coordinador_id: coordinadorId
      })
    })
    .then(response => response.json())
    .then(data => {
      if (data.valido) {
        mostrarExitoEditarCoord('CorreoPersonalEditarCoord', data.mensaje);
      } else {
        mostrarErrorEditarCoord('CorreoPersonalEditarCoord', data.mensaje);
      }
    })
    .catch(error => {
      console.error('Error en validación de correo:', error);
    });
  }

  // Event listeners para validación en tiempo real - Editar Coordinador
  document.addEventListener('DOMContentLoaded', function() {
    const inputNombresEditar = document.getElementById('inputNombresEditar');
    const inputApellidoPaternoEditar = document.getElementById('inputApellidoPaternoEditar');
    const inputApellidoMaternoEditar = document.getElementById('inputApellidoMaternoEditar');
    const inputCorreoPersonalEditar = document.getElementById('inputCorreoPersonalEditar');
    const inputDocumentoEditar = document.getElementById('inputDocumentoEditar');
    const inputTelefonoEditar = document.getElementById('inputTelefonoEditar');
    const inputNacimientoEditar = document.getElementById('inputNacimientoEditar');

    // Función para obtener el ID del coordinador en edición
    function obtenerCoordinadorId() {
      const coordinadorId = document.getElementById('modalEditarCoordinador').getAttribute('data-coordinador-id');
      return coordinadorId;
    }

    // Validación para nombres
    if (inputNombresEditar) {
      const validarNombres = debounce((valor) => {
        if (valor.trim() === '') {
          ocultarMensajesEditarCoord('NombresEditarCoord');
          return;
        }
        validarCampoEditarCoord('nombres', valor, obtenerCoordinadorId());
      }, 500, 'nombres-editar-coord');

      inputNombresEditar.addEventListener('input', function() {
        validarNombres(this.value);
      });
    }

    // Validación para apellido paterno
    if (inputApellidoPaternoEditar) {
      const validarApellidoPaterno = debounce((valor) => {
        if (valor.trim() === '') {
          ocultarMensajesEditarCoord('ApellidoPaternoEditarCoord');
          return;
        }
        validarCampoEditarCoord('apellido_paterno', valor, obtenerCoordinadorId());
      }, 500, 'apellido-paterno-editar-coord');

      inputApellidoPaternoEditar.addEventListener('input', function() {
        validarApellidoPaterno(this.value);
      });
    }

    // Validación para apellido materno
    if (inputApellidoMaternoEditar) {
      const validarApellidoMaterno = debounce((valor) => {
        if (valor.trim() === '') {
          ocultarMensajesEditarCoord('ApellidoMaternoEditarCoord');
          return;
        }
        validarCampoEditarCoord('apellido_materno', valor, obtenerCoordinadorId());
      }, 500, 'apellido-materno-editar-coord');

      inputApellidoMaternoEditar.addEventListener('input', function() {
        validarApellidoMaterno(this.value);
      });
    }

    // Validación para correo personal
    if (inputCorreoPersonalEditar) {
      const validarCorreo = debounce((valor) => {
        if (valor.trim() === '') {
          ocultarMensajesEditarCoord('CorreoPersonalEditarCoord');
          return;
        }
        validarCorreoPersonalEditarCoord(valor, obtenerCoordinadorId());
      }, 800, 'correo-editar-coord');

      inputCorreoPersonalEditar.addEventListener('input', function() {
        validarCorreo(this.value);
      });
    }

    // Validación para documento
    if (inputDocumentoEditar) {
      const validarDocumento = debounce((valor) => {
        if (valor.trim() === '') {
          ocultarMensajesEditarCoord('DocumentoEditarCoord');
          return;
        }
        validarCampoEditarCoord('documento', valor, obtenerCoordinadorId());
      }, 500, 'documento-editar-coord');

      inputDocumentoEditar.addEventListener('input', function() {
        validarDocumento(this.value);
      });
    }

    // Validación para teléfono
    if (inputTelefonoEditar) {
      const validarTelefono = debounce((valor) => {
        if (valor.trim() === '') {
          ocultarMensajesEditarCoord('TelefonoEditarCoord');
          return;
        }
        validarCampoEditarCoord('telefono', valor, obtenerCoordinadorId());
      }, 500, 'telefono-editar-coord');

      inputTelefonoEditar.addEventListener('input', function() {
        validarTelefono(this.value);
      });
    }

    // Validación para fecha de nacimiento
    if (inputNacimientoEditar) {
      const validarNacimiento = debounce((valor) => {
        if (valor.trim() === '') {
          ocultarMensajesEditarCoord('NacimientoEditarCoord');
          return;
        }
        validarCampoEditarCoord('fecha_nacimiento', valor, obtenerCoordinadorId());
      }, 500, 'nacimiento-editar-coord');

      inputNacimientoEditar.addEventListener('change', function() {
        validarNacimiento(this.value);
      });
    }

    // Limpiar mensajes al abrir el modal
    const modalEditarCoordinador = document.getElementById('modalEditarCoordinador');
    if (modalEditarCoordinador) {
      modalEditarCoordinador.addEventListener('show.bs.modal', function() {
        limpiarCamposYMensajesEditarCoord();
      });
    }

    // Manejo del envío del formulario de edición
    const formularioEditarCoord = document.getElementById('formularioEditarCoordinador');
    if (formularioEditarCoord) {
      formularioEditarCoord.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Verificar si el formulario es válido antes de enviar
        if (!formularioCoordinadorEditarEsValido()) {
          Swal.fire({
            icon: 'warning',
            title: 'Formulario incompleto',
            text: 'Por favor, complete todos los campos requeridos correctamente antes de guardar.',
            showConfirmButton: true,
            timer: 5000
          });
          return false;
        }
        
        const btnSubmit = document.getElementById('btnActualizarCoordinador');
        const btnTexto = document.getElementById('btnTextoEditarCoord');
        const btnLoading = document.getElementById('btnLoadingEditarCoord');
        
        // Deshabilitar botón y mostrar loading
        if (btnSubmit) btnSubmit.disabled = true;
        if (btnTexto) btnTexto.classList.add('hidden');
        if (btnLoading) btnLoading.classList.remove('hidden');
        
        // Enviar formulario
        this.submit();
      });
    }
  });

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
