<div
  id="modalColaboradores"
  class="fixed inset-0 w-screen h-screen bg-black/50 hidden z-50 opacity-0 transition-opacity duration-300"
  role="dialog"
  aria-modal="true"
  aria-labelledby="tituloModalColaboradores"
>
  {{-- Capa semitransparente (clic aquí cierra modal) --}}
  <div class="absolute inset-0" onclick="cerrarModalColaboradores()"></div>

  {{-- Contenedor centrado --}}
  <div class="fixed inset-0 flex items-center justify-center p-4">
    <div
      id="contenidoModalColaboradores"
      class="bg-white rounded-2xl shadow-xl w-full max-w-3xl flex flex-col overflow-hidden min-h-0 transform scale-95 transition-transform duration-300"
    >
      {{-- Cabecera fija --}}
      <header
        class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center flex-shrink-0"
      >
        <h3 id="tituloModalColaboradores" class="text-lg font-semibold text-gray-900">
          Selecciona un Colaborador
        </h3>
        <button
          type="button"
          onclick="cerrarModalColaboradores()"
          aria-label="Cerrar modal"
          class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
        >
          <i data-lucide="x" class="w-6 h-6 text-gray-500"></i>
        </button>
      </header>

      {{-- Buscador --}}
      <div class="px-6 py-4 border-b border-gray-200">
        <form id="formBuscarColaborador" class="flex flex-wrap gap-4">
          <div class="relative flex-1 min-w-64">
            <i data-lucide="search" class="absolute left-3 top-3 h-4 w-4 text-gray-400"></i>
            <input 
              type="text" 
              id="inputBuscarColaborador"
              name="searchTerm" 
              placeholder="Buscar por nombre ..."
              class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
          </div>
          <button
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
          >
            Buscar
          </button>
        </form>
      </div>

      {{-- Contenido scrollable: tabla --}}
      <div class="px-6 py-4 flex-1 overflow-y-auto max-h-[60vh] min-h-0">
        <div class="bg-white rounded-lg shadow-sm border border-gray-300 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-50 border-b border-gray-300">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Colaborador
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Teléfono
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Estado
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Acciones
                  </th>
                </tr>
              </thead>
              <tbody id="cuerpoTablaColaboradores" class="bg-white divide-y divide-gray-200">
                <!-- Filas pobladas dinámicamente -->
              </tbody>
            </table>
          </div>

          <!-- Paginación dinámica -->
          <div id="paginacionColaboradores" class="px-6 py-3 border-t border-gray-200 hidden"></div>
        </div>
      </div>

      {{-- Pie de página fijo: solo botón Cerrar --}}
      <footer
        class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end space-x-2 flex-shrink-0"
      >
        <button
          type="button"
          onclick="cerrarModalColaboradores()"
          class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50"
        >
          Cerrar
        </button>
      </footer>
    </div>
  </div>
</div>

<script>
  let yaCargoColaboradores = false;
  let paginaActualCol = 1;

  function abrirModalColaboradores() {
    const modal = document.getElementById('modalColaboradores');
    const contenido = document.getElementById('contenidoModalColaboradores');

    if (!yaCargoColaboradores) {
      cargarListaColaboradores();
      yaCargoColaboradores = true;
    }

    modal.classList.remove('hidden');
    setTimeout(() => {
      modal.classList.remove('opacity-0');
      contenido.classList.remove('scale-95');
      document.getElementById('inputBuscarColaborador').focus();
    }, 10);

    document.body.style.overflow = 'hidden';
  }

  function cerrarModalColaboradores() {
    const modal = document.getElementById('modalColaboradores');
    const contenido = document.getElementById('contenidoModalColaboradores');

    modal.classList.add('opacity-0');
    contenido.classList.add('scale-95');

    setTimeout(() => {
      modal.classList.add('hidden');
      document.body.style.overflow = 'auto';
      // Limpiar búsqueda y resultados al cerrar
      document.getElementById('inputBuscarColaborador').value = '';
      paginaActualCol = 1;
      document.getElementById('cuerpoTablaColaboradores').innerHTML = '';
      const pagDiv = document.getElementById('paginacionColaboradores');
      pagDiv.innerHTML = '';
      pagDiv.classList.add('hidden');
    }, 300);
  }

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      const modal = document.getElementById('modalColaboradores');
      if (!modal.classList.contains('hidden')) {
        cerrarModalColaboradores();
      }
    }
  });

  function seleccionarColaborador(idColaborador, nombreColaborador) {
    const inputMostrar = document.getElementById('inputMostrarCoordinador');
    inputMostrar.value = nombreColaborador;
    document.getElementById('inputHiddenCoordinador').value = idColaborador;
    cerrarModalColaboradores();
  }

  // Interceptar el submit del formulario de búsqueda
  document.getElementById('formBuscarColaborador').addEventListener('submit', function(e) {
    e.preventDefault();
    paginaActualCol = 1;
    cargarListaColaboradores();
  });

  async function cargarListaColaboradores() {
    const tbody = document.getElementById('cuerpoTablaColaboradores');
    const pagDiv = document.getElementById('paginacionColaboradores');
    const query = document.getElementById('inputBuscarColaborador').value.trim();

    tbody.innerHTML = '';
    pagDiv.innerHTML = '';
    pagDiv.classList.add('hidden');

    try {
      const idRolFiltro = 5; // Filtro por rol de Colaborador
      let url = '/admin/colaboradores/pag?search=' + encodeURIComponent(query) + '&page=' + paginaActualCol + '&filters%5Brol_id%5D=' + idRolFiltro;
      const res = await fetch(url);
      if (!res.ok) throw new Error('Error al obtener colaboradores');
      const json = await res.json();

      console.log('Colaboradores cargados:', json);

      json.data.forEach(function(colab) {
        const tr = document.createElement('tr');
        tr.classList.add('hover:bg-gray-50');

        // Formatear fecha de registro a dd/mm/yyyy (tomo created_at de usuario)
        const fecha = new Date(colab.usuario.fecha_registro).toLocaleDateString('es-PE');

        tr.innerHTML =
          '<td class="px-6 py-4 whitespace-nowrap flex items-center">' +
            '<div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">' +
              (colab.usuario.foto
                ? '<img src="/storage/avatars/' + colab.usuario.foto + '" alt="Avatar ' + colab.nombres + '" class="w-full h-full object-cover" />'
                : '<span class="text-sm font-medium text-gray-600">' +
                    (colab.apellido_paterno.charAt(0) + colab.apellido_materno.charAt(0)).toUpperCase() +
                  '</span>'
              ) +
            '</div>' +
            '<div class="ml-4">' +
              '<p class="text-sm font-medium text-gray-900">' +
                colab.nombres + ' ' + colab.apellido_paterno + ' ' + colab.apellido_materno +
              '</p>' +
              '<p class="text-sm text-gray-500">' + colab.correo + '</p>' +
            '</div>' +
          '</td>' +

          '<td class="px-6 py-4 whitespace-nowrap">' +
            '<p class="text-sm text-gray-800">' + (colab.telefono || '- Sin teléfono -') + '</p>' +
          '</td>' +

          '<td class="px-6 py-4 whitespace-nowrap">' +
            '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ' +
              (colab.usuario.activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') +
            '">' +
              (colab.usuario.activo ? 'Activo' : 'Inactivo') +
            '</span>' +
          '</td>' +

          '<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">' +
            '<button onclick="seleccionarColaborador(' + colab.id + ', \'' +
              (colab.nombres + ' ' + colab.apellido_paterno + ' ' + colab.apellido_materno).replace(/'/g, "\\'") +
            '\')" class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-colors">' +
              '<i data-lucide="check" class="w-4 h-4 mr-1"></i> Seleccionar' +
            '</button>' +
          '</td>';

        tbody.appendChild(tr);
      });

      // Generar paginación si existe json.links
      if (json.links) {
        pagDiv.classList.remove('hidden');
        json.links.forEach(function(link) {
          const btn = document.createElement('button');
          btn.innerHTML = link.label;
          btn.disabled = !link.url;

          btn.classList.add('mx-1', 'px-3', 'py-1', 'text-sm', 'rounded-lg', 'transition-colors');

          if (link.active) {
            // Página actual: fondo azul y texto blanco
            btn.classList.add('bg-blue-600', 'text-white');
          } else {
            // Página inactiva: fondo blanco, texto gris, borde y hover gris
            btn.classList.add('bg-white', 'text-gray-700', 'border', 'border-gray-300', 'hover:bg-gray-100');
          }

          if (link.url) {
            btn.addEventListener('click', function() {
              const params = new URLSearchParams(new URL(link.url).search);
              paginaActualCol = params.get('page') || 1;
              cargarListaColaboradores();
            });
          }

          pagDiv.appendChild(btn);
        });
      }


      // Reconstruir íconos Lucide en cada renderizado
      lucide.createIcons();
    } catch (err) {
      console.error(err);
      tbody.innerHTML =
        '<tr>' +
          '<td colspan="4" class="px-6 py-12 text-center text-red-500">' +
            'No se pudieron cargar los colaboradores. Inténtalo más tarde.' +
          '</td>' +
        '</tr>';
    }
  }
</script>

