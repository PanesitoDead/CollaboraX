@extends('layouts.private.colaborador')

@section('title', 'Configuración')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Configuración</h1>
    </div>

    {{-- Container --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-300 overflow-hidden">
        {{-- Tabs --}}
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button
                    class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                    data-tab="perfil"
                >
                    <i data-lucide="user" class="w-4 h-4 inline mr-2"></i> Perfil
                </button>
                <button
                    class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                    data-tab="seguridad"
                >
                    <i data-lucide="lock" class="w-4 h-4 inline mr-2"></i> Seguridad
                </button>
            </nav>
        </div>

        {{-- Tab Contents --}}
        <div class="p-6">
            <div id="perfil-tab" class="tab-content">
                @include('partials.colaborador.perfil-form')
            </div>
            <div id="seguridad-tab" class="tab-content hidden">
                @include('partials.colaborador.seguridad-form')
            </div>
        </div>
    </div>

    {{-- Toast Container --}}
    <div id="toast-container" class="fixed top-4 right-4 space-y-2 z-50"></div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Tabs
    const tabs = document.querySelectorAll('.tab-button');
    const panes = document.querySelectorAll('.tab-content');
    const activate = tab => {
        tabs.forEach(btn => {
            btn.classList.remove('border-blue-600','text-blue-600');
            btn.classList.add('border-transparent','text-gray-500');
        });
        panes.forEach(p => p.classList.add('hidden'));

        const btn = document.querySelector(`.tab-button[data-tab="${tab}"]`);
        btn.classList.remove('border-transparent','text-gray-500');
        btn.classList.add('border-blue-600','text-blue-600');
        document.getElementById(`${tab}-tab`).classList.remove('hidden');
    };
    // Init
    activate('perfil');
    tabs.forEach(btn => btn.addEventListener('click', () => activate(btn.dataset.tab)));
    
    // Toast helper
    window.showToast = (msg, type='success') => {
        const colors = {
            success: 'bg-green-50 border-green-200 text-green-800',
            error:   'bg-red-50   border-red-200   text-red-800',
            info:    'bg-blue-50  border-blue-200  text-blue-800'
        };
        const toast = document.createElement('div');
        toast.className = `flex items-center p-4 mb-4 text-sm rounded-lg border ${colors[type]} shadow transition transform translate-x-full opacity-0`;
        toast.innerHTML = `
            <i data-lucide="${ type==='success'?'check-circle': type==='error'?'x-circle':'info'}" class="w-5 h-5 mr-2"></i>
            <span>${msg}</span>
            <button class="ml-auto -mx-1.5 -my-1.5 p-1.5 rounded-lg hover:bg-gray-100" onclick="this.parentElement.remove()">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        `;
        document.getElementById('toast-container').appendChild(toast);
        if(window.lucide) lucide.createIcons();
        setTimeout(() => {
            toast.classList.remove('translate-x-full','opacity-0');
        }, 100);
        setTimeout(() => {
            toast.classList.add('translate-x-full','opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    };

    // Form handler
    window.handleFormSubmit = (formId, url, successMsg) => {
        const form = document.getElementById(formId);
        const fd = new FormData(form);
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: fd
        })
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                showToast(data.message||successMsg,'success');
                if(data.redirect) setTimeout(() => window.location = data.redirect, 1500);
            } else showToast(data.message||'Error al procesar','error');
        })
        .catch(() => showToast('Error al procesar','error'));
    };

    // Avatar preview
    const inp = document.getElementById('avatar');
    const prev = document.getElementById('avatar-preview');
    if(inp && prev) {
        inp.addEventListener('change', e => {
            const f = e.target.files[0];
            if(!f) return;
            const reader = new FileReader();
            reader.onload = e => prev.src = e.target.result;
            reader.readAsDataURL(f);
        });
    }
});
</script>
@endpush
