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
            <div id="errorNombreEmpresa" class="text-red-500 text-xs mt-1 hidden"></div>
            <div id="successNombreEmpresa" class="text-green-500 text-xs mt-1 hidden"></div>
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
            <div id="errorDescripcionEmpresa" class="text-red-500 text-xs mt-1 hidden"></div>
            <div id="successDescripcionEmpresa" class="text-green-500 text-xs mt-1 hidden"></div>
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
              <div id="errorRucEmpresa" class="text-red-500 text-xs mt-1 hidden"></div>
              <div id="successRucEmpresa" class="text-green-500 text-xs mt-1 hidden"></div>
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
              <div id="errorTelefonoEmpresa" class="text-red-500 text-xs mt-1 hidden"></div>
              <div id="successTelefonoEmpresa" class="text-green-500 text-xs mt-1 hidden"></div>
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
            id="btnActualizarEmpresa"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed"
          >
            <span id="textoEnviarEmpresa">Actualizar Empresa</span>
            <span id="btnLoadingEmpresa" class="hidden">
              <i class="fas fa-spinner fa-spin mr-2"></i>Validando...
            </span>
          </button>
        </footer>
      </form>
    </div>
  </div>
</div>

<script>
  let idEmpresaActual = null;

  // Variables para debounce
  let timeoutNombreEmpresa = null;
  let timeoutDescripcionEmpresa = null;
  let timeoutRucEmpresa = null;
  let timeoutTelefonoEmpresa = null;

  // Variable para rastrear el estado de validación de cada campo
  let estadosValidacionEmpresa = {
    nombre: null,           // null = no validado, true = válido, false = inválido
    descripcion: true,      // opcional
    ruc: null,              // requerido
    telefono: true          // opcional
  };

  // Función para verificar si el formulario de empresa es válido
  function formularioEmpresaEsValido() {
    // Verificar que todos los campos requeridos estén validados y sean válidos
    return estadosValidacionEmpresa.nombre === true && 
           estadosValidacionEmpresa.ruc === true;
    // Los campos opcionales no afectan la validez si están en null o true
  }

  // Función para actualizar el estado del botón de envío
  function actualizarBotonEnvioEmpresa() {
    const btnEnviar = document.getElementById('btnActualizarEmpresa');
    if (!btnEnviar) return;
    
    if (formularioEmpresaEsValido()) {
      btnEnviar.disabled = false;
      btnEnviar.classList.remove('bg-gray-400', 'cursor-not-allowed');
      btnEnviar.classList.add('bg-blue-600', 'hover:bg-blue-700');
    } else {
      btnEnviar.disabled = true;
      btnEnviar.classList.remove('bg-blue-600', 'hover:bg-blue-700');
      btnEnviar.classList.add('bg-gray-400', 'cursor-not-allowed');
    }
  }

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

      // Limpiar mensajes y estilos previos
      limpiarCamposYMensajesEmpresa();

      document.getElementById('inputNombreEmpresa').value = data.nombre ?? '';
      document.getElementById('inputDescripcionEmpresa').value = data.descripcion ?? '';
      document.getElementById('inputRucEmpresa').value = data.ruc ?? '';
      document.getElementById('inputTelefonoEmpresa').value = data.telefono ?? '';
      document.getElementById('inputCorreoEmpresa').value = data.correo ?? '';
      document.getElementById('inputActivoEmpresa').value = data.activo ? '1' : '0';
      document.getElementById('vistaPreviaAvatar').src = '/images/default-avatar.png';

      // Validar campos prellenados y actualizar estados
      setTimeout(() => {
        // Validar campos requeridos que vienen prellenados
        if (data.nombre) {
          estadosValidacionEmpresa.nombre = true;
        }
        if (data.ruc) {
          estadosValidacionEmpresa.ruc = true;
        }
        
        // Los campos opcionales ya están en true por defecto
        actualizarBotonEnvioEmpresa();
      }, 100);

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
   * Limpia todos los campos y mensajes del modal de empresa
   */
  function limpiarCamposYMensajesEmpresa() {
    // Limpiar mensajes de error y éxito
    const mensajes = ['NombreEmpresa', 'DescripcionEmpresa', 'RucEmpresa', 'TelefonoEmpresa'];
    mensajes.forEach(campo => {
      const errorElement = document.getElementById(`error${campo}`);
      const successElement = document.getElementById(`success${campo}`);
      if (errorElement) errorElement.classList.add('hidden');
      if (successElement) successElement.classList.add('hidden');
    });

    // Resetear estilos de los inputs
    const inputs = document.querySelectorAll('#modalEmpresa input[type="text"], #modalEmpresa input[type="tel"], #modalEmpresa textarea');
    inputs.forEach(input => {
      input.classList.remove('border-red-500', 'border-green-500');
      input.classList.add('border-gray-300');
    });

    // Resetear estados de validación
    estadosValidacionEmpresa = {
      nombre: null,
      descripcion: true,   // opcional
      ruc: null,
      telefono: true       // opcional
    };

    // Actualizar botón
    actualizarBotonEnvioEmpresa();
  }

  /**
   * Muestra mensaje de error para empresa
   */
  function mostrarErrorEmpresa(campo, mensaje) {
    const errorElement = document.getElementById(`error${campo}`);
    const successElement = document.getElementById(`success${campo}`);
    
    // Mapear nombres de campo para encontrar los inputs correctos y estados de validación
    let inputId = '';
    let campoValidacion = '';
    switch (campo) {
      case 'NombreEmpresa':
        inputId = 'inputNombreEmpresa';
        campoValidacion = 'nombre';
        break;
      case 'DescripcionEmpresa':
        inputId = 'inputDescripcionEmpresa';
        campoValidacion = 'descripcion';
        break;
      case 'RucEmpresa':
        inputId = 'inputRucEmpresa';
        campoValidacion = 'ruc';
        break;
      case 'TelefonoEmpresa':
        inputId = 'inputTelefonoEmpresa';
        campoValidacion = 'telefono';
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
      estadosValidacionEmpresa[campoValidacion] = false;
      actualizarBotonEnvioEmpresa();
    }
  }

  /**
   * Muestra mensaje de éxito para empresa
   */
  function mostrarExitoEmpresa(campo, mensaje) {
    const errorElement = document.getElementById(`error${campo}`);
    const successElement = document.getElementById(`success${campo}`);
    
    // Mapear nombres de campo para encontrar los inputs correctos y estados de validación
    let inputId = '';
    let campoValidacion = '';
    switch (campo) {
      case 'NombreEmpresa':
        inputId = 'inputNombreEmpresa';
        campoValidacion = 'nombre';
        break;
      case 'DescripcionEmpresa':
        inputId = 'inputDescripcionEmpresa';
        campoValidacion = 'descripcion';
        break;
      case 'RucEmpresa':
        inputId = 'inputRucEmpresa';
        campoValidacion = 'ruc';
        break;
      case 'TelefonoEmpresa':
        inputId = 'inputTelefonoEmpresa';
        campoValidacion = 'telefono';
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
      estadosValidacionEmpresa[campoValidacion] = true;
      actualizarBotonEnvioEmpresa();
    }
  }

  /**
   * Oculta todos los mensajes de un campo para empresa
   */
  function ocultarMensajesEmpresa(campo) {
    const errorElement = document.getElementById(`error${campo}`);
    const successElement = document.getElementById(`success${campo}`);
    
    // Mapear nombres de campo para encontrar los inputs correctos y estados de validación
    let inputId = '';
    let campoValidacion = '';
    switch (campo) {
      case 'NombreEmpresa':
        inputId = 'inputNombreEmpresa';
        campoValidacion = 'nombre';
        break;
      case 'DescripcionEmpresa':
        inputId = 'inputDescripcionEmpresa';
        campoValidacion = 'descripcion';
        break;
      case 'RucEmpresa':
        inputId = 'inputRucEmpresa';
        campoValidacion = 'ruc';
        break;
      case 'TelefonoEmpresa':
        inputId = 'inputTelefonoEmpresa';
        campoValidacion = 'telefono';
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
      if (['descripcion', 'telefono'].includes(campoValidacion)) {
        estadosValidacionEmpresa[campoValidacion] = true;
      } else {
        estadosValidacionEmpresa[campoValidacion] = null;
      }
      actualizarBotonEnvioEmpresa();
    }
  }

  /**
   * Valida un campo mediante AJAX para empresa
   */
  function validarCampoEmpresa(campo, valor) {
    if (!valor.trim()) {
      ocultarMensajesEmpresa(campo);
      return;
    }

    // Mapear los nombres de campos del frontend a los del backend
    let campoBackend = campo.toLowerCase().replace('empresa', '');
    switch (campo) {
      case 'NombreEmpresa':
        campoBackend = 'nombre';
        break;
      case 'DescripcionEmpresa':
        campoBackend = 'descripcion';
        break;
      case 'RucEmpresa':
        campoBackend = 'ruc';
        break;
      case 'TelefonoEmpresa':
        campoBackend = 'telefono';
        break;
    }

    console.log('Validando campo empresa:', campo, 'Backend:', campoBackend, 'Valor:', valor);

    fetch('/super-admin/empresas/validar-campo', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({
        campo: campoBackend,
        valor: valor,
        empresa_id: idEmpresaActual
      })
    })
    .then(response => response.json())
    .then(data => {
      console.log('Respuesta del servidor empresa:', data);
      if (data.valido) {
        mostrarExitoEmpresa(campo, data.mensaje);
      } else {
        mostrarErrorEmpresa(campo, data.mensaje);
      }
    })
    .catch(error => {
      console.error('Error en la validación de empresa:', error);
      mostrarErrorEmpresa(campo, 'Error de conexión al validar el campo.');
    });
  }

  /**
   * Valida que todos los campos del formulario de empresa sean válidos
   */
  function validarFormularioCompletoEmpresa() {
    const nombre = document.getElementById('inputNombreEmpresa').value.trim();
    const ruc = document.getElementById('inputRucEmpresa').value.trim();

    // Verificar que todos los campos requeridos estén llenos
    if (!nombre || !ruc) {
      return false;
    }

    // Verificar que no haya mensajes de error visibles
    const errores = document.querySelectorAll('#modalEmpresa [id^="error"]:not(.hidden)');
    return errores.length === 0;
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

  // Event listeners para validaciones en tiempo real
  document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up empresa validators');
    
    // Inicializar el botón como deshabilitado
    actualizarBotonEnvioEmpresa();
    
    // Validación de nombre
    const inputNombreEmpresa = document.getElementById('inputNombreEmpresa');
    if (inputNombreEmpresa) {
      inputNombreEmpresa.addEventListener('input', function() {
        clearTimeout(timeoutNombreEmpresa);
        timeoutNombreEmpresa = setTimeout(() => {
          validarCampoEmpresa('NombreEmpresa', this.value);
        }, 500);
      });
    }

    // Validación de descripción
    const inputDescripcionEmpresa = document.getElementById('inputDescripcionEmpresa');
    if (inputDescripcionEmpresa) {
      inputDescripcionEmpresa.addEventListener('input', function() {
        clearTimeout(timeoutDescripcionEmpresa);
        timeoutDescripcionEmpresa = setTimeout(() => {
          validarCampoEmpresa('DescripcionEmpresa', this.value);
        }, 500);
      });
    }

    // Validación de RUC
    const inputRucEmpresa = document.getElementById('inputRucEmpresa');
    if (inputRucEmpresa) {
      inputRucEmpresa.addEventListener('input', function() {
        clearTimeout(timeoutRucEmpresa);
        timeoutRucEmpresa = setTimeout(() => {
          validarCampoEmpresa('RucEmpresa', this.value);
        }, 500);
      });
    }

    // Validación de teléfono
    const inputTelefonoEmpresa = document.getElementById('inputTelefonoEmpresa');
    if (inputTelefonoEmpresa) {
      inputTelefonoEmpresa.addEventListener('input', function() {
        clearTimeout(timeoutTelefonoEmpresa);
        timeoutTelefonoEmpresa = setTimeout(() => {
          validarCampoEmpresa('TelefonoEmpresa', this.value);
        }, 500);
      });
    }

    // Manejo del envío del formulario de empresa
    const formularioEmpresa = document.getElementById('formularioEmpresa');
    if (formularioEmpresa) {
      formularioEmpresa.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Verificar si el formulario es válido antes de enviar
        if (!formularioEmpresaEsValido()) {
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'warning',
              title: 'Formulario incompleto',
              text: 'Por favor, complete todos los campos requeridos correctamente antes de guardar.',
              showConfirmButton: true,
              timer: 5000
            });
          } else {
            alert('Por favor, complete todos los campos requeridos correctamente antes de guardar.');
          }
          return false;
        }
        
        const btnSubmit = document.getElementById('btnActualizarEmpresa');
        const btnTexto = document.getElementById('textoEnviarEmpresa');
        const btnLoading = document.getElementById('btnLoadingEmpresa');
        
        // Deshabilitar botón y mostrar loading
        if (btnSubmit) btnSubmit.disabled = true;
        if (btnTexto) btnTexto.classList.add('hidden');
        if (btnLoading) btnLoading.classList.remove('hidden');
        
        // Validar todos los campos antes de enviar
        if (validarFormularioCompletoEmpresa()) {
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
  document.addEventListener('DOMContentLoaded', function() {
    const avatarInput = document.getElementById('avatarEmpresa');
    if (avatarInput) {
      avatarInput.addEventListener('change', function (e) {
        const archivo = e.target.files[0];
        const imgPreview = document.getElementById('vistaPreviaAvatar');
        if (archivo) {
          const lector = new FileReader();
          lector.onload = () => {
            imgPreview.src = lector.result;
            imgPreview.classList.remove('hidden');
          };
          lector.readAsDataURL(archivo);
        }
      });
    }
  });

  // Cerrar modal cuando se presiona la tecla "Escape"
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && idEmpresaActual !== null) {
      cerrarModalEmpresa();
    }
  });
</script>
