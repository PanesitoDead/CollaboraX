<div
  id="modalColaborador"
  class="fixed inset-0 w-screen h-screen bg-black/50 hidden z-50 opacity-0 transition-opacity duration-300"
  role="dialog"
  aria-modal="true"
  aria-labelledby="tituloModalColaborador"
>
  <!-- Capa semitransparente (clic aquí cierra modal) -->
  <div class="absolute inset-0" onclick="cerrarModalColaborador()"></div>

  <!-- Contenedor centrado -->
  <div class="fixed inset-0 flex items-center justify-center p-4">
    <div
      id="contenidoModalColaborador"
      class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] flex flex-col overflow-hidden transform scale-95 transition-transform duration-300"
      style="height: fit-content; min-height: 300px;"
    >
      <form
        id="formularioColaborador"
        method="POST"
        action="{{ route('admin.colaboradores.store') }}"
        class="flex flex-col h-full"
      >
        @csrf
        <!-- Cabecera fija -->
        <header
          class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center flex-shrink-0"
        >
          <h3 id="tituloModalColaborador" class="text-lg font-semibold text-gray-900">
            Crear Colaborador
          </h3>
          <button
            type="button"
            onclick="cerrarModalColaborador()"
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
              for="inputNombresColaborador"
              class="block mb-1 text-sm font-medium text-gray-700"
            >
              Nombres
            </label>
            <input
              type="text"
              name="nombres"
              id="inputNombresColaborador"
              required
              class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
            <div id="errorNombres" class="text-red-500 text-xs mt-1 hidden"></div>
            <div id="successNombres" class="text-green-500 text-xs mt-1 hidden"></div>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Apellido Paterno -->
            <div>
              <label
                for="inputApellidoPaternoColaborador"
                class="block mb-1 text-sm font-medium text-gray-700"
              >
                Apellido Paterno
              </label>
              <input
                type="text"
                name="apellido_paterno"
                id="inputApellidoPaternoColaborador"
                required
                class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
              <div id="errorApellidoPaterno" class="text-red-500 text-xs mt-1 hidden"></div>
              <div id="successApellidoPaterno" class="text-green-500 text-xs mt-1 hidden"></div>
            </div>
            <!-- Apellido Materno -->
            <div>
              <label
                for="inputApellidoMaternoColaborador"
                class="block mb-1 text-sm font-medium text-gray-700"
              >
                Apellido Materno
              </label>
              <input
                type="text"
                name="apellido_materno"
                id="inputApellidoMaternoColaborador"
                required
                class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
              <div id="errorApellidoMaterno" class="text-red-500 text-xs mt-1 hidden"></div>
              <div id="successApellidoMaterno" class="text-green-500 text-xs mt-1 hidden"></div>
            </div>
          </div> 
          <!-- Correo personal -->
          <div>
            <label
              for="inputCorreoPersonal"
              class="block mb-1 text-sm font-medium text-gray-700"
            >
              Correo Personal
            </label>
            <input
              type="email"
              name="correo_personal"
              id="inputCorreoPersonal"
              required
              class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="micorreo@ejemplo.com"
            />
            <div id="errorCorreoPersonal" class="text-red-500 text-xs mt-1 hidden"></div>
            <div id="successCorreoPersonal" class="text-green-500 text-xs mt-1 hidden"></div>
          </div>
          <!-- Correo generado -->
          <div>
            <label
              for="inputCorreoColaborador"
              class="block mb-1 text-sm font-medium text-gray-700"
            >
              Correo Corporativo
            </label>
            <div class="flex items-center">
              <input
                type="text"
                name="correo"
                id="inputCorreoColaborador"
                readonly
                class="flex-1 mt-1 px-3 py-2 border border-gray-300 rounded-l-lg bg-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="nombre.apellidoPaterno.apellidoMaterno"
              />
              <span
                id="dominioColaborador"
                class="mt-1 px-3 py-2 border border-l-0 border-gray-300 rounded-r-lg bg-gray-200 text-gray-700 select-none"
              >
                @nombreEmpresa.cx.com
              </span>
            </div>
            <div id="errorCorreoCorporativo" class="text-red-500 text-xs mt-1 hidden"></div>
            <div id="successCorreoCorporativo" class="text-green-500 text-xs mt-1 hidden"></div>
          </div>

          <!-- Contraseña generada + botones -->
          <div>
            <label
              for="inputClaveColaborador"
              class="block mb-1 text-sm font-medium text-gray-700"
            >
              Contraseña Generada
            </label>
            <div class="flex items-center space-x-2">
              <input
                type="text"
                id="inputClaveColaborador"
                name="clave"
                readonly
                class="flex-1 mt-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
              <!-- Botón regenerar -->
              <button
                type="button"
                onclick="regenerarClave()"
                class="mt-1 p-2.5 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition"
                title="Regenerar contraseña"
              >
                <i data-lucide="refresh-ccw" class="w-5 h-5 text-gray-600"></i>
              </button>
              <!-- Botón copiar -->
              <button
                type="button"
                onclick="copiarClave()"
                class="mt-1 p-2.5 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition"
                title="Copiar contraseña"
              >
                <i data-lucide="clipboard" class="w-5 h-5 text-gray-600"></i>
              </button>
            </div>
            <p class="mt-2 text-xs text-gray-500">
              Nota: El usuario podrá cambiar esta contraseña una vez inicie sesión.
            </p>
          </div>
        </div>

        <!-- Pie de página fijo -->
        <footer
          class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end space-x-2 flex-shrink-0"
        >
          <button
            type="button"
            onclick="cerrarModalColaborador()"
            class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            Cancelar
          </button>
          <button
            type="submit"
            id="btnCrearColaborador"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-opacity"
          >
            <span id="btnTexto">Crear Colaborador</span>
            <span id="btnLoading" class="hidden">
              <i class="fas fa-spinner fa-spin mr-2"></i>Validando...
            </span>
          </button>
        </footer>
      </form>
    </div>
  </div>
</div>

<script>
  // Variables globales para el debounce
  let timeoutCorreoPersonal = null;
  let timeoutCorreoCorporativo = null;
  let timeoutNombres = null;
  let timeoutApellidoPaterno = null;
  let timeoutApellidoMaterno = null;

  // Variable para rastrear el estado de validación de cada campo
  let estadosValidacionColaborador = {
    nombres: null,           // null = no validado, true = válido, false = inválido
    apellidoPaterno: null,
    apellidoMaterno: null,
    correoPersonal: null,
    correoCorporativo: null
  };

  // Función para verificar si el formulario es válido
  function formularioColaboradorEsValido() {
    // Verificar que todos los campos requeridos estén validados y sean válidos
    return estadosValidacionColaborador.nombres === true && 
           estadosValidacionColaborador.apellidoPaterno === true && 
           estadosValidacionColaborador.apellidoMaterno === true &&
           estadosValidacionColaborador.correoPersonal === true &&
           estadosValidacionColaborador.correoCorporativo === true;
  }

  // Función para actualizar el estado del botón de envío
  function actualizarBotonEnvioColaborador() {
    const btnEnviar = document.getElementById('btnCrearColaborador');
    if (formularioColaboradorEsValido()) {
      btnEnviar.disabled = false;
      btnEnviar.classList.remove('opacity-50', 'cursor-not-allowed');
    } else {
      btnEnviar.disabled = true;
      btnEnviar.classList.add('opacity-50', 'cursor-not-allowed');
    }
  }

  function abrirModalCrearColaborador(dominio) {
    // Guardamos el dominio (sin arroba)
    document.getElementById('dominioColaborador').textContent = '@' + dominio + '.cx.com';

    // Limpiar campos y mensajes
    limpiarCamposYMensajes();

    // Generar una contraseña inicial
    document.getElementById('inputClaveColaborador').value = generarClave();

    // Mostrar modal con animación
    const modal = document.getElementById('modalColaborador');
    const contenido = document.getElementById('contenidoModalColaborador');
    modal.classList.remove('hidden');
    setTimeout(() => {
      modal.classList.remove('opacity-0');
      contenido.classList.remove('scale-95');
    }, 10);
    document.body.style.overflow = 'hidden';

    // Inicializar el estado del botón (debería estar deshabilitado al inicio)
    actualizarBotonEnvioColaborador();
  }

  /**
   * Limpia todos los campos y mensajes del modal
   */
  function limpiarCamposYMensajes() {
    // Limpiar campos
    document.getElementById('inputNombresColaborador').value = "";
    document.getElementById('inputApellidoPaternoColaborador').value = "";
    document.getElementById('inputApellidoMaternoColaborador').value = "";
    document.getElementById('inputCorreoPersonal').value = "";
    document.getElementById('inputCorreoColaborador').value = "";
    document.getElementById('inputClaveColaborador').value = "";

    // Limpiar mensajes de error y éxito
    const mensajes = ['Nombres', 'ApellidoPaterno', 'ApellidoMaterno', 'CorreoPersonal', 'CorreoCorporativo'];
    mensajes.forEach(campo => {
      document.getElementById(`error${campo}`).classList.add('hidden');
      document.getElementById(`success${campo}`).classList.add('hidden');
    });

    // Resetear estilos de los inputs
    const inputs = document.querySelectorAll('#modalColaborador input');
    inputs.forEach(input => {
      input.classList.remove('border-red-500', 'border-green-500');
      input.classList.add('border-gray-300');
    });

    // Resetear estados de validación
    estadosValidacionColaborador = {
      nombres: null,
      apellidoPaterno: null,
      apellidoMaterno: null,
      correoPersonal: null,
      correoCorporativo: null
    };

    // Actualizar botón (debería quedar deshabilitado)
    actualizarBotonEnvioColaborador();
  }

  /**
   * Cierra el modal de creación de colaborador.
   */
  function cerrarModalColaborador() {
    const modal = document.getElementById('modalColaborador');
    const contenido = document.getElementById('contenidoModalColaborador');
    modal.classList.add('opacity-0');
    contenido.classList.add('scale-95');
    setTimeout(() => {
      modal.classList.add('hidden');
      document.body.style.overflow = 'auto';
    }, 300);
  }

  /**
   * Genera una contraseña aleatoria de 8 caracteres (letras y números).
   * @returns {string}
   */
  function generarClave() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let pass = '';
    for (let i = 0; i < 8; i++) {
      pass += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return pass;
  }

  /**
   * Regenera la contraseña y la asigna al input.
   */
  function regenerarClave() {
    const nuevoPass = generarClave();
    document.getElementById('inputClaveColaborador').value = nuevoPass;
  }

  /**
   * Copia la contraseña actual al portapapeles.
   */
  function copiarClave() {
    const inputPass = document.getElementById('inputClaveColaborador');
    inputPass.select();
    inputPass.setSelectionRange(0, 99999); // Para móviles
    document.execCommand('copy');
    // Opcional: mostrar un breve tooltip o mensaje
    alert('Contraseña copiada al portapapeles.');
  }

  /**
   * Construye el correo corporativo en base a los campos Nombre, Apellidos, y dominio.
   */
  function actualizarCorreo() {
    const nombre = document.getElementById('inputNombresColaborador').value.trim();
    const apPaterno = document.getElementById('inputApellidoPaternoColaborador').value.trim();
    const apMaterno = document.getElementById('inputApellidoMaternoColaborador').value.trim();

    if (!nombre || !apPaterno || !apMaterno) {
      document.getElementById('inputCorreoColaborador').value = '';
      // Resetear estado del correo corporativo si no se puede generar
      estadosValidacionColaborador.correoCorporativo = null;
      actualizarBotonEnvioColaborador();
      return;
    }

    // Formato: nombre.apellidoPaterno.apellidoMaterno@dominio
    const parteNombre = nombre.toLowerCase().replace(/\s+/g, '.');
    const parteApPaterno = apPaterno.toLowerCase().replace(/\s+/g, '');
    const parteApMaterno = apMaterno.toLowerCase().replace(/\s+/g, '');
    const correoGenerado = `${parteNombre}.${parteApPaterno}.${parteApMaterno}`;
    document.getElementById('inputCorreoColaborador').value = correoGenerado;

    // Validar el correo corporativo generado automáticamente
    setTimeout(() => {
      validarCorreoCorporativo();
    }, 100);
  }

  /**
   * Muestra mensaje de error
   */
  function mostrarError(campo, mensaje) {
    const errorElement = document.getElementById(`error${campo}`);
    const successElement = document.getElementById(`success${campo}`);
    
    // Mapear nombres de campo para encontrar los inputs correctos y estados de validación
    let inputId = '';
    let campoValidacion = '';
    switch (campo) {
      case 'Nombres':
        inputId = 'inputNombresColaborador';
        campoValidacion = 'nombres';
        break;
      case 'ApellidoPaterno':
        inputId = 'inputApellidoPaternoColaborador';
        campoValidacion = 'apellidoPaterno';
        break;
      case 'ApellidoMaterno':
        inputId = 'inputApellidoMaternoColaborador';
        campoValidacion = 'apellidoMaterno';
        break;
      case 'CorreoPersonal':
        inputId = 'inputCorreoPersonal';
        campoValidacion = 'correoPersonal';
        break;
      case 'CorreoCorporativo':
        inputId = 'inputCorreoColaborador';
        campoValidacion = 'correoCorporativo';
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
      estadosValidacionColaborador[campoValidacion] = false;
      actualizarBotonEnvioColaborador();
    }
  }

  /**
   * Muestra mensaje de éxito
   */
  function mostrarExito(campo, mensaje) {
    const errorElement = document.getElementById(`error${campo}`);
    const successElement = document.getElementById(`success${campo}`);
    
    // Mapear nombres de campo para encontrar los inputs correctos y estados de validación
    let inputId = '';
    let campoValidacion = '';
    switch (campo) {
      case 'Nombres':
        inputId = 'inputNombresColaborador';
        campoValidacion = 'nombres';
        break;
      case 'ApellidoPaterno':
        inputId = 'inputApellidoPaternoColaborador';
        campoValidacion = 'apellidoPaterno';
        break;
      case 'ApellidoMaterno':
        inputId = 'inputApellidoMaternoColaborador';
        campoValidacion = 'apellidoMaterno';
        break;
      case 'CorreoPersonal':
        inputId = 'inputCorreoPersonal';
        campoValidacion = 'correoPersonal';
        break;
      case 'CorreoCorporativo':
        inputId = 'inputCorreoColaborador';
        campoValidacion = 'correoCorporativo';
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
      estadosValidacionColaborador[campoValidacion] = true;
      actualizarBotonEnvioColaborador();
    }
  }

  /**
   * Oculta todos los mensajes de un campo
   */
  function ocultarMensajes(campo) {
    const errorElement = document.getElementById(`error${campo}`);
    const successElement = document.getElementById(`success${campo}`);
    
    // Mapear nombres de campo para encontrar los inputs correctos y estados de validación
    let inputId = '';
    let campoValidacion = '';
    switch (campo) {
      case 'Nombres':
        inputId = 'inputNombresColaborador';
        campoValidacion = 'nombres';
        break;
      case 'ApellidoPaterno':
        inputId = 'inputApellidoPaternoColaborador';
        campoValidacion = 'apellidoPaterno';
        break;
      case 'ApellidoMaterno':
        inputId = 'inputApellidoMaternoColaborador';
        campoValidacion = 'apellidoMaterno';
        break;
      case 'CorreoPersonal':
        inputId = 'inputCorreoPersonal';
        campoValidacion = 'correoPersonal';
        break;
      case 'CorreoCorporativo':
        inputId = 'inputCorreoColaborador';
        campoValidacion = 'correoCorporativo';
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
      estadosValidacionColaborador[campoValidacion] = null;
      actualizarBotonEnvioColaborador();
    }
  }

  /**
   * Valida un campo mediante AJAX
   */
  function validarCampo(campo, valor) {
    if (!valor.trim()) {
      ocultarMensajes(campo);
      return;
    }

    // Mapear los nombres de campos del frontend a los del backend
    let campoBackend = campo.toLowerCase();
    switch (campo) {
      case 'Nombres':
        campoBackend = 'nombres';
        break;
      case 'ApellidoPaterno':
        campoBackend = 'apellido_paterno';
        break;
      case 'ApellidoMaterno':
        campoBackend = 'apellido_materno';
        break;
    }

    console.log('Validando campo:', campo, 'Backend:', campoBackend, 'Valor:', valor); // Debug

    fetch('{{ route("admin.colaboradores.validar-campo") }}', {
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
      console.log('Respuesta del servidor:', data); // Debug
      if (data.valido) {
        mostrarExito(campo, data.mensaje);
      } else {
        mostrarError(campo, data.mensaje);
      }
    })
    .catch(error => {
      console.error('Error en la validación:', error);
      mostrarError(campo, 'Error de conexión al validar el campo.');
    });
  }

  /**
   * Valida el correo personal
   */
  function validarCorreoPersonal() {
    const correoPersonal = document.getElementById('inputCorreoPersonal').value.trim();
    
    if (!correoPersonal) {
      ocultarMensajes('CorreoPersonal');
      return;
    }

    fetch('{{ route("admin.colaboradores.validar-correo-personal") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({
        correo_personal: correoPersonal
      })
    })
    .then(response => response.json())
    .then(data => {
      if (data.valido) {
        mostrarExito('CorreoPersonal', data.mensaje);
      } else {
        mostrarError('CorreoPersonal', data.mensaje);
      }
    })
    .catch(error => {
      console.error('Error en la validación del correo personal:', error);
    });
  }

  /**
   * Valida el correo corporativo
   */
  function validarCorreoCorporativo() {
    const correo = document.getElementById('inputCorreoColaborador').value.trim();
    
    if (!correo) {
      ocultarMensajes('CorreoCorporativo');
      return;
    }

    fetch('{{ route("admin.colaboradores.validar-correo-corporativo") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({
        correo: correo
      })
    })
    .then(response => response.json())
    .then(data => {
      if (data.valido) {
        mostrarExito('CorreoCorporativo', data.mensaje);
      } else {
        mostrarError('CorreoCorporativo', data.mensaje);
      }
    })
    .catch(error => {
      console.error('Error en la validación del correo corporativo:', error);
    });
  }

  // Event listeners con debounce
  document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up validators'); // Debug
    
    // Validación de nombres
    const inputNombres = document.getElementById('inputNombresColaborador');
    if (inputNombres) {
      inputNombres.addEventListener('input', function() {
        clearTimeout(timeoutNombres);
        timeoutNombres = setTimeout(() => {
          console.log('Validating Nombres:', this.value); // Debug
          validarCampo('Nombres', this.value);
          actualizarCorreo();
        }, 500);
      });
    }

    // Validación de apellido paterno
    const inputApellidoPaterno = document.getElementById('inputApellidoPaternoColaborador');
    if (inputApellidoPaterno) {
      inputApellidoPaterno.addEventListener('input', function() {
        clearTimeout(timeoutApellidoPaterno);
        timeoutApellidoPaterno = setTimeout(() => {
          console.log('Validating ApellidoPaterno:', this.value); // Debug
          validarCampo('ApellidoPaterno', this.value);
          actualizarCorreo();
        }, 500);
      });
    }

    // Validación de apellido materno
    const inputApellidoMaterno = document.getElementById('inputApellidoMaternoColaborador');
    if (inputApellidoMaterno) {
      inputApellidoMaterno.addEventListener('input', function() {
        clearTimeout(timeoutApellidoMaterno);
        timeoutApellidoMaterno = setTimeout(() => {
          console.log('Validating ApellidoMaterno:', this.value); // Debug
          validarCampo('ApellidoMaterno', this.value);
          actualizarCorreo();
        }, 500);
      });
    }

    // Validación de correo personal
    const inputCorreoPersonal = document.getElementById('inputCorreoPersonal');
    if (inputCorreoPersonal) {
      inputCorreoPersonal.addEventListener('input', function() {
        clearTimeout(timeoutCorreoPersonal);
        timeoutCorreoPersonal = setTimeout(() => {
          console.log('Validating CorreoPersonal:', this.value); // Debug
          validarCorreoPersonal();
        }, 800);
      });
    }

    // Manejo del envío del formulario
    const formulario = document.getElementById('formularioColaborador');
    if (formulario) {
      formulario.addEventListener('submit', function(e) {
        // Verificar si hay campos con errores visibles
        const erroresVisibles = document.querySelectorAll('#modalColaborador .text-red-500:not(.hidden)');
        
        if (!formularioColaboradorEsValido() || erroresVisibles.length > 0) {
          e.preventDefault();
          alert('Por favor, corrige los errores en el formulario antes de continuar.');
          return false;
        }
        
        const btnSubmit = document.getElementById('btnCrearColaborador');
        const btnTexto = document.getElementById('btnTexto');
        const btnLoading = document.getElementById('btnLoading');
        
        // Deshabilitar botón y mostrar loading
        if (btnSubmit) btnSubmit.disabled = true;
        if (btnTexto) btnTexto.classList.add('hidden');
        if (btnLoading) btnLoading.classList.remove('hidden');
      });
    }

    // Inicializar el estado del botón al cargar
    actualizarBotonEnvioColaborador();
  });

  // Cerrar modal al presionar "Escape"
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !document.getElementById('modalColaborador').classList.contains('hidden')) {
      cerrarModalColaborador();
    }
  });

  // Función de test para debugging (llamar desde la consola)
  window.testValidaciones = function() {
    console.log('Probando validaciones...');
    validarCampo('Nombres', 'Juan');
    validarCampo('ApellidoPaterno', 'Pérez');
    validarCampo('ApellidoMaterno', 'García');
  };
</script>
