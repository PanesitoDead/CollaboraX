@if(session('toast'))
<div class="fixed bottom-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
    <p class="font-bold">{{ session('toast.title') }}</p>
    <p>{{ session('toast.message') }}</p>
</div>
@endif