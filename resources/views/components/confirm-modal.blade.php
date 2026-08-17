@props([
    'id' => 'confirm-modal',
    'title' => 'Confirmation',
    'action',
    'confirmText' => 'Confirmer',
    'cancelText' => 'Annuler',
])

<div id="{{ $id }}" class="fixed inset-0 bg-black/20 hidden items-center justify-center z-180 backdrop-blur-xs">
    <div class="bg-white rounded-md p-8 w-[12cm] text-center">
        <i class="fa-solid fa-circle-exclamation text-4xl text-rouge mb-3"></i>
        <h3 class="text-xl font-semibold mb-2">{{ $title }}</h3>
        <p class="text-black/60 mb-5">{{ $slot }}</p>
        <div class="flex justify-center gap-3">
            <button type="button" onclick="closeModal('{{ $id }}')" class="border border-black/10 rounded-md px-5 py-2">
                {{ $cancelText }}
            </button>
            <form action="{{ $action }}" method="POST">
                @csrf
                <button type="submit" class="bg-rouge text-white rounded-md px-5 py-2">
                    {{ $confirmText }}
                </button>
            </form>
        </div>
    </div>
</div>