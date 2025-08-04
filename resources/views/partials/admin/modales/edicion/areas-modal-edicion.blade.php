<div
  id="modalArea"
  class="fixed inset-0 w-screen h-screen bg-black/50 hidden z-50 opacity-0 transition-opacity duration-300"
  role="dialog"
  aria-modal="true"
  aria-labelledby="tituloModalArea"
  data-area-id=""
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
                <!-- Mensajes de validación para nombre -->
                <div id="errorNombreArea" class="text-red-500 text-sm mt-1 hidden"></div>
                <div id="successNombreArea" class="text-green-500 text-sm mt-1 hidden"></div>
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
                <!-- Mensajes de validación para código -->
                <div id="errorCodigoArea" class="text-red-500 text-sm mt-1 hidden"></div>
                <div id="successCodigoArea" class="text-green-500 text-sm mt-1 hidden"></div>
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
              <!-- Mensajes de validación para descripción -->
              <div id="errorDescripcionArea" class="text-red-500 text-sm mt-1 hidden"></div>
              <div id="successDescripcionArea" class="text-green-500 text-sm mt-1 hidden"></div>
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
            id="btnEnviarArea"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
          >
            <svg id="loadingSpinnerArea" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white hidden" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
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
   * Limpia todos los campos y mensajes del modal de área
   */
  function limpiarCamposYMensajesArea() {
    // Limpiar mensajes de error y éxito
    const mensajes = ['NombreArea', 'CodigoArea', 'DescripcionArea'];
    mensajes.forEach(campo => {
      const errorElement = document.getElementById(`error${campo}`);
      const successElement = document.getElementById(`success${campo}`);
      if (errorElement) errorElement.classList.add('hidden');
      if (successElement) successElement.classList.add('hidden');
    });

    // Resetear estilos de los inputs
    const inputs = document.querySelectorAll('#modalArea input[type="text"], #modalArea textarea');
    inputs.forEach(input => {
      input.classList.remove('border-red-500', 'border-green-500');
      input.classList.add('border-gray-300');
    });

    // Resetear estados de validación
    estadosValidacionArea = {
      nombre: null,
      codigo: null,
      descripcion: true // descripción es opcional
    };

    // Actualizar botón
    actualizarBotonEnvioArea();
  }
  
  /**
   * Abre el modal de Área. 
   * Si recibe un id (número/string), asume edición y hace fetch para cargar datos.
   * Si id es null o undefined, abre en modo "crear" (POST).
   */
  async function abrirAreaModal(id, isAsignar = false) {
    idAreaActual = id;
    const formulario = document.getElementById('areaForm');
    const methodField = document.getElementById('campoMetodoArea');
    const titulo = document.getElementById('tituloModalArea');
    const textoSubmit = document.getElementById('textoEnviarArea');
    const modal = document.getElementById('modalArea');

    // Establecer el ID del área en el atributo data
    modal.setAttribute('data-area-id', id || '');
    
    // Limpiar mensajes de validación
    limpiarCamposYMensajesArea();

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

        const nombres = [
        data?.coordinador_nombres,
        data?.coordinador_apellido_paterno,
        data?.coordinador_apellido_materno
      ].filter(Boolean).join(' ');

        // Rellenar valores en el formulario
        document.getElementById('inputNombreArea').value = data.nombre ?? '';
        document.getElementById('inputCodigoArea').value = data.codigo ?? '';
        document.getElementById('inputDescripcionArea').value = data.descripcion ?? '';
        document.getElementById('selectColorArea').value = data.color ?? '';
        document.getElementById('selectEstadoArea').value = data.activo? 1 : 0;
        document.getElementById('inputMostrarCoordinador').value = nombres || 'Ningún colaborador seleccionado';
        document.getElementById('inputHiddenCoordinador').value = data?.coordinador_id ?? '';

        // Validar campos iniciales para habilitar/deshabilitar el botón
        setTimeout(() => {
          if (data.nombre) validarCampoArea('nombre', data.nombre, idAreaActual);
          if (data.codigo) validarCampoArea('codigo', data.codigo, idAreaActual);
          if (data.descripcion) validarCampoArea('descripcion', data.descripcion, idAreaActual);
        }, 100);
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
      
      // En modo creación, deshabilitar el botón hasta que se validen los campos
      actualizarBotonEnvioArea();
    }

    // Mostrar modal con animación
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

  // Funciones de validación para áreas
  function mostrarErrorArea(campo, mensaje) {
    const errorElement = document.getElementById(`error${campo}`);
    const successElement = document.getElementById(`success${campo}`);
    
    let inputId = '';
    let campoValidacion = '';
    switch (campo) {
      case 'NombreArea': 
        inputId = 'inputNombreArea'; 
        campoValidacion = 'nombre';
        break;
      case 'CodigoArea': 
        inputId = 'inputCodigoArea'; 
        campoValidacion = 'codigo';
        break;
      case 'DescripcionArea': 
        inputId = 'inputDescripcionArea'; 
        campoValidacion = 'descripcion';
        break;
    }
    
    const inputElement = document.getElementById(inputId);
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
      estadosValidacionArea[campoValidacion] = false;
      actualizarBotonEnvioArea();
    }
  }

  function mostrarExitoArea(campo, mensaje) {
    const errorElement = document.getElementById(`error${campo}`);
    const successElement = document.getElementById(`success${campo}`);
    
    let inputId = '';
    let campoValidacion = '';
    switch (campo) {
      case 'NombreArea': 
        inputId = 'inputNombreArea'; 
        campoValidacion = 'nombre';
        break;
      case 'CodigoArea': 
        inputId = 'inputCodigoArea'; 
        campoValidacion = 'codigo';
        break;
      case 'DescripcionArea': 
        inputId = 'inputDescripcionArea'; 
        campoValidacion = 'descripcion';
        break;
    }
    
    const inputElement = document.getElementById(inputId);
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
      estadosValidacionArea[campoValidacion] = true;
      actualizarBotonEnvioArea();
    }
  }

  function ocultarMensajesArea(campo) {
    const errorElement = document.getElementById(`error${campo}`);
    const successElement = document.getElementById(`success${campo}`);
    
    let inputId = '';
    let campoValidacion = '';
    switch (campo) {
      case 'NombreArea': 
        inputId = 'inputNombreArea'; 
        campoValidacion = 'nombre';
        break;
      case 'CodigoArea': 
        inputId = 'inputCodigoArea'; 
        campoValidacion = 'codigo';
        break;
      case 'DescripcionArea': 
        inputId = 'inputDescripcionArea'; 
        campoValidacion = 'descripcion';
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
    if (campoValidacion && campoValidacion !== 'descripcion') {
      estadosValidacionArea[campoValidacion] = null;
      actualizarBotonEnvioArea();
    }
  }

  // Variables para debounce
  let timeoutsArea = {};

  // Variable para rastrear el estado de validación de cada campo
  let estadosValidacionArea = {
    nombre: null,    // null = no validado, true = válido, false = inválido
    codigo: null,
    descripcion: true // descripción es opcional, así que por defecto es válida
  };

  // Función para verificar si el formulario es válido
  function formularioAreaEsValido() {
    // Verificar que todos los campos requeridos estén validados y sean válidos
    return estadosValidacionArea.nombre === true && 
           estadosValidacionArea.codigo === true && 
           (estadosValidacionArea.descripcion === true || estadosValidacionArea.descripcion === null);
  }

  // Función para actualizar el estado del botón de envío
  function actualizarBotonEnvioArea() {
    const btnEnviar = document.getElementById('btnEnviarArea');
    if (formularioAreaEsValido()) {
      btnEnviar.disabled = false;
      btnEnviar.classList.remove('opacity-50', 'cursor-not-allowed');
    } else {
      btnEnviar.disabled = true;
      btnEnviar.classList.add('opacity-50', 'cursor-not-allowed');
    }
  }

  // Función de debounce genérica para áreas
  function debounceArea(func, wait, key) {
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeoutsArea[key]);
        delete timeoutsArea[key];
        func(...args);
      };
      clearTimeout(timeoutsArea[key]);
      timeoutsArea[key] = setTimeout(later, wait);
    };
  }

  // Función para validar campos de área
  function validarCampoArea(campo, valor, areaId = null) {
    fetch('{{ route("validar.campo.area.edicion") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({
        campo: campo,
        valor: valor,
        area_id: areaId
      })
    })
    .then(response => response.json())
    .then(data => {
      // Mapear correctamente los nombres de campo del backend al frontend
      let campoFrontend = '';
      switch (campo) {
        case 'nombre':
          campoFrontend = 'NombreArea';
          break;
        case 'codigo':
          campoFrontend = 'CodigoArea';
          break;
        case 'descripcion':
          campoFrontend = 'DescripcionArea';
          break;
        default:
          campoFrontend = campo.charAt(0).toUpperCase() + campo.slice(1) + 'Area';
      }
      
      if (data.valido) {
        mostrarExitoArea(campoFrontend, data.mensaje);
      } else {
        mostrarErrorArea(campoFrontend, data.mensaje);
      }
    })
    .catch(error => {
      console.error('Error en validación de área:', error);
    });
  }

  // Event listeners para validación en tiempo real - Editar Área
  document.addEventListener('DOMContentLoaded', function() {
    const inputNombreArea = document.getElementById('inputNombreArea');
    const inputCodigoArea = document.getElementById('inputCodigoArea');
    const inputDescripcionArea = document.getElementById('inputDescripcionArea');
    const formularioArea = document.getElementById('areaForm');

    // Función para obtener el ID del área en edición
    function obtenerAreaId() {
      const areaId = document.getElementById('modalArea').getAttribute('data-area-id');
      return areaId;
    }

    // Validar formulario antes del envío
    if (formularioArea) {
      formularioArea.addEventListener('submit', function(e) {
        // Verificar si hay campos con errores visibles
        const erroresVisibles = document.querySelectorAll('#modalArea .text-red-500:not(.hidden)');
        
        if (!formularioAreaEsValido() || erroresVisibles.length > 0) {
          e.preventDefault();
          alert('Por favor, corrige los errores en el formulario antes de continuar.');
          return false;
        }
      });
    }

    // Validación para nombre del área
    if (inputNombreArea) {
      const validarNombre = debounceArea((valor) => {
        if (valor.trim() === '') {
          ocultarMensajesArea('NombreArea');
          return;
        }
        validarCampoArea('nombre', valor, obtenerAreaId());
      }, 500, 'nombre-area');

      inputNombreArea.addEventListener('input', function() {
        validarNombre(this.value);
      });
    }

    // Validación para código del área
    if (inputCodigoArea) {
      const validarCodigo = debounceArea((valor) => {
        if (valor.trim() === '') {
          ocultarMensajesArea('CodigoArea');
          return;
        }
        validarCampoArea('codigo', valor, obtenerAreaId());
      }, 500, 'codigo-area');

      inputCodigoArea.addEventListener('input', function() {
        validarCodigo(this.value);
      });
    }

    // Validación para descripción del área (opcional)
    if (inputDescripcionArea) {
      const validarDescripcion = debounceArea((valor) => {
        if (valor.trim() === '') {
          ocultarMensajesArea('DescripcionArea');
          return;
        }
        validarCampoArea('descripcion', valor, obtenerAreaId());
      }, 700, 'descripcion-area');

      inputDescripcionArea.addEventListener('input', function() {
        validarDescripcion(this.value);
      });
    }

    // Limpiar mensajes al abrir el modal
    const modalArea = document.getElementById('modalArea');
    if (modalArea) {
      // Observer para detectar cuando el modal se abre
      const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
          if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
            const target = mutation.target;
            if (!target.classList.contains('hidden') && !target.classList.contains('opacity-0')) {
              limpiarCamposYMensajesArea();
            }
          }
        });
      });
      observer.observe(modalArea, { attributes: true });
    }
  });

  // Cerrar modal cuando se presiona la tecla "Escape"
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && idAreaActual !== null) {
      cerrarAreaModal();
    }
  });

</script>
