<!-- Modal Detalles de Empresa -->
<div id="detallesEmpresaModal" class="fixed inset-0 bg-black/50 hidden z-50 opacity-0 transition-opacity duration-300" role="dialog" aria-modal="true" aria-labelledby="tituloModalDetallesEmpresa">
  <div class="flex items-center justify-center min-h-screen p-4">
    <div id="detallesEmpresaContent" class="bg-white rounded-2xl shadow-xl max-w-lg w-full transform scale-95 transition-transform duration-300">
      
      <!-- Cabecera -->
      <div class="px-6 py-4 flex justify-between items-center border-b border-gray-200 bg-gray-50 rounded-t-2xl">
        <h3 id="tituloModalDetallesEmpresa" class="text-lg font-semibold text-gray-900">Detalles de la Empresa</h3>
        <button type="button" onclick="cerrarModalDetallesEmpresa()" class="p-2 rounded-lg hover:bg-gray-100 transition-colors" aria-label="Cerrar detalles de empresa">
          <i data-lucide="x" class="w-6 h-6 text-gray-500"></i>
        </button>
      </div>
      
      <!-- Cuerpo -->
      <div class="px-6 py-4 space-y-4 max-h-[70vh] overflow-y-auto">
        <!-- Encabezado con avatar -->
        <div class="flex items-center space-x-4 mb-4">
          <div class="w-18 h-18 rounded-full overflow-hidden border-4 border-gray-300 bg-gray-100 flex items-center justify-center relative">
            <img id="avatarEmpresa" src="" alt="Avatar de la empresa" class="w-full h-full object-cover absolute top-0 left-0 hidden rounded-full"/>
            <span id="avatarIniciales" class="text-base font-semibold text-gray-700 z-10"></span>
          </div>
          <div>
            <h4 id="nombreEmpresa" class="text-lg font-medium text-gray-900"></h4>
            <p id="emailEmpresa" class="text-sm text-gray-500"></p>
          </div>
        </div>

        <!-- Datos principales -->
        <div class="grid grid-cols-2 gap-4 text-sm">
          <div>
            <p class="font-semibold text-gray-700 mb-1">Plan de Servicio</p>
            <p id="planEmpresa" class="text-gray-900"></p>
          </div>
          <div>
            <p class="font-semibold text-gray-700 mb-1">RUC</p>
            <p id="rucEmpresa" class="text-gray-900"></p>
          </div>
          <div>
            <p class="font-semibold text-gray-700 mb-1">Teléfono</p>
            <p id="telefonoEmpresa" class="text-gray-900"></p>
          </div>
          <div>
            <p class="font-semibold text-gray-700 mb-1">Estado</p>
            <span id="estadoEmpresa" class="inline-block px-2 py-1 text-xs font-semibold rounded-full"></span>
          </div>
          <div class="col-span-2">
            <p class="font-semibold text-gray-700 mb-1">Descripción</p>
            <p id="descripcionEmpresa" class="text-gray-900"></p>
          </div>
          <div>
            <p class="font-semibold text-gray-700 mb-1">Fecha de Registro</p>
            <p id="fechaRegistroEmpresa" class="text-gray-900"></p>
          </div>
          <div>
            <p class="font-semibold text-gray-700 mb-1">Número de Usuarios</p>
            <p class="text-gray-900 flex items-center text-xs">
              <i data-lucide="users" class="w-4 h-4 mr-1 text-gray-500"></i>
              <span id="usuariosEmpresa"></span>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  let empresaActualId = null;
  
  async function abrirModalDetallesEmpresa(id) {
    empresaActualId = id;
    const url = `/super-admin/empresas/${id}`; // Ruta relativa al backend

    try {
      const respuesta = await fetch(url);
      if (!respuesta.ok) throw new Error('No se recibieron datos de la empresa');
      const data = await respuesta.json();

      const img = document.getElementById('avatarEmpresa');
      const initialsEl = document.getElementById('avatarIniciales');
      // Extraemos avatar_url y nombre del objeto data
      const avatarUrl = data.avatar_url;
      const nombreEmpresa = data.nombre || ''; // por si acaso viene vacío o indefinido

      if (avatarUrl) {
        // Si existe avatar_url, lo asignamos al <img> y lo mostramos
        img.src = avatarUrl;
        // Cuando la imagen cargue correctamente, la hacemos visible y ocultamos el span de iniciales
        img.onload = () => {
          img.classList.remove('hidden');
          initialsEl.classList.add('hidden');
        };
        // Si la imagen falla al cargar (404, etc.), muestra las iniciales:
        img.onerror = () => {
          // calcular iniciales y mostrarlas
          const iniciales = nombreEmpresa.substr(0, 2).toUpperCase();
          initialsEl.textContent = iniciales;

          img.classList.add('hidden');       // aseguramos que la imagen quede oculta
          initialsEl.classList.remove('hidden');
        };
      } else {
        // No hay avatar_url: calculamos las iniciales y las mostramos en el <span>
        const iniciales = nombreEmpresa.substr(0, 2).toUpperCase();
        initialsEl.textContent = iniciales;

        img.classList.add('hidden');
        initialsEl.classList.remove('hidden');
      }

      document.getElementById('nombreEmpresa').textContent = data.nombre;
      document.getElementById('emailEmpresa').textContent = data.correo;
      document.getElementById('planEmpresa').textContent = data.plan_servicio?.nombre || '—';
      document.getElementById('rucEmpresa').textContent = data.ruc || 'No registrado';
      document.getElementById('telefonoEmpresa').textContent = data.telefono || 'No registrado';

      // Estado con estilos
      const estadoEl = document.getElementById('estadoEmpresa');
      const activo = data.status === 'active';
      estadoEl.textContent = activo ? 'Activo' : 'Inactivo';
      estadoEl.className = `
        inline-block px-2 py-1 text-xs font-semibold rounded-full
        ${activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}
      `;

      document.getElementById('descripcionEmpresa').textContent = data.descripcion || 'Sin descripción';
      // Fecha formateada para Perú en español
      const fecha = new Date(data.usuario.fecha_registro);
      document.getElementById('fechaRegistroEmpresa').textContent =
        fecha.toLocaleDateString('es-PE', { day: 'numeric', month: 'long', year: 'numeric' });

      document.getElementById('usuariosEmpresa').textContent = data.nro_usuarios || '0';

      // Mostrar modal con animación
      const modal = document.getElementById('detallesEmpresaModal');
      const contenido = document.getElementById('detallesEmpresaContent');
      modal.classList.remove('hidden');
      setTimeout(() => {
        modal.classList.remove('opacity-0');
        contenido.classList.remove('scale-95');
      }, 10);
      document.body.style.overflow = 'hidden';

    } catch (error) {
      console.error(error);
      alert('Error al cargar los detalles de la empresa. Intenta nuevamente.');
    }
  }

  /**
   * Cierra el modal y limpia el estado.
   */
  function cerrarModalDetallesEmpresa() {
    const modal = document.getElementById('detallesEmpresaModal');
    const contenido = document.getElementById('detallesEmpresaContent');
    modal.classList.add('opacity-0');
    contenido.classList.add('scale-95');
    setTimeout(() => {
      modal.classList.add('hidden');
      document.body.style.overflow = 'auto';
      empresaActualId = null;
    }, 300);
  }

  // Cerrar al hacer clic fuera del contenido
  document.getElementById('detallesEmpresaModal')
    .addEventListener('click', e => {
      if (e.target === e.currentTarget) {
        cerrarModalDetallesEmpresa();
      }
    });

  // Cerrar con la tecla Escape
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && empresaActualId !== null) {
      cerrarModalDetallesEmpresa();
    }
  });
</script>
