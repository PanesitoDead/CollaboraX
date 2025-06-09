<!-- Modal Detalles de Actividad -->
<div id="activityModal" class="fixed inset-0 w-screen h-screen bg-black/50 hidden z-50 opacity-0 transition-opacity duration-300" role="dialog" aria-modal="true" aria-labelledby="tituloModalActividad">
  <div class="flex items-center justify-center min-h-screen p-4">
    <div id="activityContent" class="bg-white rounded-2xl shadow-xl max-w-lg w-full transform scale-95 transition-transform duration-300">
      
      <!-- Cabecera -->
      <div class="px-6 py-4 flex justify-between items-center border-b border-gray-200 bg-gray-50 rounded-t-2xl">
        <h3 id="tituloModalActividad" class="text-lg font-semibold text-gray-900">Detalle de Actividad</h3>
        <button type="button" onclick="closeActivityModal()" class="p-2 rounded-lg hover:bg-gray-100 transition-colors" aria-label="Cerrar detalles de actividad">
          <i data-lucide="x" class="w-6 h-6 text-gray-500"></i>
        </button>
      </div>
      
      <!-- Cuerpo -->
      <div id="modalBody" class="px-6 py-4 space-y-6 max-h-[70vh] overflow-y-auto">
        <!-- Aquí se inyecta el contenido dinámico -->
      </div>

      <!-- Pie del Modal -->
      <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-2 rounded-b-2xl">
        <button type="button" onclick="closeActivityModal()" class="inline-flex justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
          Cerrar
        </button>
      </div>

    </div>
  </div>
</div>

@push('scripts')
<script>
  let actividadActualId = null;

  async function abrirModalActividad(id) {
    actividadActualId = id;
    const url = `/colaborador/actividades/${id}`; // Ruta real al backend

    try {
      const respuesta = await fetch(url);
      if (!respuesta.ok) throw new Error('Error al obtener detalles');
      const a = await respuesta.json();

      document.getElementById('modalBody').innerHTML = generateModalContent(a);
      const modal = document.getElementById('activityModal');
      const content = document.getElementById('activityContent');

      modal.classList.remove('hidden');
      setTimeout(() => {
        modal.classList.remove('opacity-0');
        content.classList.remove('scale-95');
      }, 10);
      document.body.style.overflow = 'hidden';
    } catch (err) {
      console.error(err);
      alert('No se pudo cargar la actividad. Intenta nuevamente.');
    }
  }

  function closeActivityModal() {
    const modal = document.getElementById('activityModal');
    const content = document.getElementById('activityContent');
    modal.classList.add('opacity-0');
    content.classList.add('scale-95');
    setTimeout(() => {
      modal.classList.add('hidden');
      document.body.style.overflow = 'auto';
      actividadActualId = null;
    }, 300);
  }

  // Cierra al clic fuera del contenido\ n  document.getElementById('activityModal').addEventListener('click', e => {
    if (e.target === e.currentTarget && actividadActualId !== null) {
      closeActivityModal();
    }
    

  // Cierra con Escape
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && actividadActualId !== null) {
      closeActivityModal();
    }
  });

  function generateModalContent(a) {
    const colores = {
      'Completo': ['bg-green-100','text-green-800'],
      'Incompleta': ['bg-yellow-100','text-yellow-800'],
      'En Proceso': ['bg-blue-100','text-blue-800'],
      'Suspendida': ['bg-red-100','text-red-800']
    };
    const [bg, text] = colores[a.estado] || ['bg-gray-100','text-gray-800'];
    return `
      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <h4 class="text-lg font-medium text-gray-900">${a.nombre}</h4>
        </div>
        <div>
          <p class="text-sm font-semibold text-gray-700">Descripción</p>
          <p class="mt-1 text-sm text-gray-600">${a.descripcion}</p>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <p class="text-sm font-semibold text-gray-700">Fecha límite</p>
            <p class="mt-1 text-sm text-gray-600">${new Date(a.fecha_entrega).toLocaleDateString('es-PE',{day:'numeric',month:'long',year:'numeric'})}</p>
          </div>
          <div>
            <p class="text-sm font-semibold text-gray-700">Equipo</p>
            <p class="mt-1 text-sm text-gray-600">${a.equipo}</p>
          </div>
        </div>
        <div>
          <p class="text-sm font-semibold text-gray-700">Estado</p>
          <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-xs font-medium ${bg} ${text}">${a.estado}</span>
        </div>
        ${a.meta ? `
        <div>
          <p class="text-sm font-semibold text-gray-700">Meta asociada</p>
          <p class="mt-1 text-sm text-gray-600">${a.meta}</p>
        </div>` : ``}
        ${a.asignado_por ? `
        <div>
          <p class="text-sm font-semibold text-gray-700">Asignado por</p>
          <p class="mt-1 text-sm text-gray-600">${a.asignado_por}</p>
        </div>` : ``}
      </div>
    `;
  }
</script>
@endpush
