<!-- Modal Detalles del Coordinador -->
<div id="detallesCoordinadorModal" class="fixed inset-0 w-screen h-screen bg-black/50 hidden z-50 opacity-0 transition-opacity duration-300" role="dialog" aria-modal="true" aria-labelledby="tituloModalDetallesCoordinador">
  <div class="flex items-center justify-center min-h-screen p-4">
    <div id="detallesCoordinadorContent" class="bg-white rounded-2xl shadow-xl max-w-lg w-full transform scale-95 transition-transform duration-300">
      
      <!-- Cabecera -->
      <div class="px-6 py-4 flex justify-between items-center border-b border-gray-200 bg-gray-50 rounded-t-2xl">
        <h3 id="tituloModalDetallesCoordinador" class="text-lg font-semibold text-gray-900">Detalles del Coordinador</h3>
        <button type="button" onclick="cerrarModalDetallesCoordinador()" class="p-2 rounded-lg hover:bg-gray-100 transition-colors" aria-label="Cerrar detalles del colaborador">
          <i data-lucide="x" class="w-6 h-6 text-gray-500"></i>
        </button>
      </div>
      
      <!-- Cuerpo -->
      <div class="px-6 py-4 space-y-4 max-h-[70vh] overflow-y-auto">
        <!-- Encabezado con avatar -->
        <div class="flex items-center space-x-4 mb-4">
          <div class="w-18 h-18 rounded-full overflow-hidden border-4 border-gray-300 bg-gray-100 flex items-center justify-center relative">
            <img id="avatarCoordinador" src="" alt="Avatar del colaborador" class="w-full h-full object-cover absolute top-0 left-0 hidden rounded-full"/>
            <span id="avatarInicialesCoordinador" class="text-base font-semibold text-gray-700 z-10"></span>
          </div>
          <div>
            <h4 id="nombreCoordinador" class="text-lg font-medium text-gray-900"></h4>
            <p id="emailCoordinador" class="text-sm text-gray-500"></p>
          </div>
        </div>

        <!-- Datos principales -->
        <div class="grid grid-cols-2 gap-4 text-sm">
          <div>
            <p class="font-semibold text-gray-700 mb-1">Estado</p>
            <span id="estadoCoordinador" class="inline-block px-2 py-1 text-xs font-semibold rounded-full"></span>
          </div>
          <div>
            <p class="font-semibold text-gray-700 mb-1">Área</p>
            <p id="areaCoordinador" class="text-gray-900"></p>
          </div>
          <div>
            <p class="font-semibold text-gray-700 mb-1">Fecha de Registro</p>
            <p id="fechaRegistroCoordinador" class="text-gray-900"></p>
          </div>
          <div>
            <p class="font-semibold text-gray-700 mb-1">Equipo Asignado</p>
            <p id="equipoCoordinador" class="text-gray-900"></p>
          </div>
          <div>
            <p class="font-semibold text-gray-700 mb-1">Teléfono</p>
            <p id="telefonoCoordinador" class="text-gray-900">—</p>
          </div>
          <div>
            <p class="font-semibold text-gray-700 mb-1">Doc. Identidad</p>
            <p id="docIdentidadCoordinador" class="text-gray-900">—</p>
          </div>
          <div class="col-span-2">
            <p class="font-semibold text-gray-700 mb-1">Estadísticas</p>
            <div class="grid grid-cols-3 gap-4 pt-2">
              <div class="text-center">
                <p id="contadorMeta" class="text-2xl font-semibold text-blue-600">—</p>
                <p class="text-sm text-gray-600">Metas</p>
              </div>
              <div class="text-center">
                <p id="contadorTareas" class="text-2xl font-semibold text-green-600">—</p>
                <p class="text-sm text-gray-600">Tareas</p>
              </div>
              <div class="text-center">
                <p id="contadorReuniones" class="text-2xl font-semibold text-purple-600">—</p>
                <p class="text-sm text-gray-600">Reuniones</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pie del Modal -->
      <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-2 rounded-b-2xl">
      </div>

    </div>
  </div>
</div>

<script>
  let colaboradorActualId = null;

  async function abrirModalDetallesCoordinador(id) {
    colaboradorActualId = id;
    const url = `/admin/coordinadores-equipos/${id}`; // Ajusta la ruta según tu backend real

    try {
      const respuesta = await fetch(url);
      if (!respuesta.ok) throw new Error('No se recibieron datos del colaborador');
      const data = await respuesta.json();

      // Elementos del DOM
      const img = document.getElementById('avatarCoordinador');
      const initialsEl = document.getElementById('avatarInicialesCoordinador');
      const nombreEl = document.getElementById('nombreCoordinador');
      const emailEl = document.getElementById('emailCoordinador');
      const estadoEl = document.getElementById('estadoCoordinador');
      const areaEl = document.getElementById('areaCoordinador');
      const fechaEl = document.getElementById('fechaRegistroCoordinador');
      const equipoEl = document.getElementById('equipoCoordinador');
      const telefonoEl = document.getElementById('telefonoCoordinador');
      const docIdentidadEl = document.getElementById('docIdentidadCoordinador');
      const contadorMetaEl = document.getElementById('contadorMeta');
      const contadorTarEl = document.getElementById('contadorTareas');
      const contadorReuEl = document.getElementById('contadorReuniones');

      // Asignar nombre y correo
      nombreEl.textContent = data.nombres + ' ' + data.apellido_paterno + ' ' + data.apellido_materno || 'Sin nombre';
      emailEl.textContent = data.correo || 'Sin correo';

      // Avatar / Iniciales
      const avatarUrl = data.avatar_url || '';
      const apPaterno = (data.apellido_paterno || '').toUpperCase().substring(0, 1);
      const apMaterno = (data.apellido_materno || '').toUpperCase().substring(0, 1);
      const iniciales = apPaterno + apMaterno;

      if (avatarUrl) {
        img.src = avatarUrl;
        img.onload = () => {
          img.classList.remove('hidden');
          initialsEl.classList.add('hidden');
        };
        img.onerror = () => {
          initialsEl.textContent = iniciales;
          img.classList.add('hidden');
          initialsEl.classList.remove('hidden');
        };
      } else {
        initialsEl.textContent = iniciales;
        img.classList.add('hidden');
        initialsEl.classList.remove('hidden');
      }

      // Estado
      const activo = data.estado === 'activo';
      estadoEl.textContent = activo ? 'Activo' : 'Inactivo';
      estadoEl.className = `
        inline-block px-2 py-1 text-xs font-semibold rounded-full
        ${activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}
      `;

      // Área
      areaEl.textContent = data.area?.nombre || 'Sin área';

      // Fecha de registro (asumiendo que viene en formato ISO)
      if (data.usuario.fecha_registro) {
        const fechaObj = new Date(data.usuario.fecha_registro);
        fechaEl.textContent = fechaObj.toLocaleDateString('es-PE', { day: 'numeric', month: 'long', year: 'numeric' });
      } else {
        fechaEl.textContent = '—';
      }

      // Equipo asignado
      equipoEl.textContent = data.equipo?.nombre || 'Sin equipo';
      // Teléfono
      telefonoEl.textContent = data.telefono || '—';
      // Documento de identidad
      docIdentidadEl.textContent = data.doc_identidad || '—';

      // Contadores
      contadorMetaEl.textContent = data.nro_metas ? '1' : '0';
      contadorTarEl.textContent = data.nro_tareas != null ? data.nro_tareas : '0';
      contadorReuEl.textContent = data.nro_reuniones != null ? data.nro_reuniones : '0';

      // Mostrar modal con animación
      const modal = document.getElementById('detallesCoordinadorModal');
      const contenido = document.getElementById('detallesCoordinadorContent');
      modal.classList.remove('hidden');
      setTimeout(() => {
        modal.classList.remove('opacity-0');
        contenido.classList.remove('scale-95');
      }, 10);
      document.body.style.overflow = 'hidden';

    } catch (error) {
      console.error(error);
      alert('Error al cargar los detalles del colaborador. Intenta nuevamente.');
    }
  }

  function cerrarModalDetallesCoordinador() {
    const modal = document.getElementById('detallesCoordinadorModal');
    const contenido = document.getElementById('detallesCoordinadorContent');
    modal.classList.add('opacity-0');
    contenido.classList.add('scale-95');
    setTimeout(() => {
      modal.classList.add('hidden');
      document.body.style.overflow = 'auto';
      colaboradorActualId = null;
    }, 300);
  }

  // Cerrar al hacer clic fuera del contenido
  document.getElementById('detallesCoordinadorModal')
    .addEventListener('click', e => {
      if (e.target === e.currentTarget) {
        cerrarModalDetallesCoordinador();
      }
    });

  // Cerrar con la tecla Escape
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && colaboradorActualId !== null) {
      cerrarModalDetallesCoordinador();
    }
  });
</script>
