@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="w-full py-3">
    <div class="">
        <a href="">Retour / </a>
        <span class="font-semibold">Creation</span>
    </div>
    <div class="bg-white rounded-md me-2">
        <h2 class="text-2xl font-semibold my-2 text-vert border-b--2 border-black/10">
            Ajouter une question — {{ $glisserdeposer->titre }}
        </h2>
        @if($errors->any())
            <div class="mb-4 p-3 rounded-md bg-red-50 border border-rouge text-rouge text-sm">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        <form id="gd-form" action="{{ route('prof.examen.glisserdeposer.question.store', [$slug, $examen->id, $glisserdeposer->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium">Énoncé (optionnel)</label>
                <textarea 
                    name="enonce" 
                    rows="2" 
                    class="border border-black/20 rounded w-full p-2" 
                    placeholder="Ex: Placez chaque élément à sa place sur le schéma"
                    >{{ old('enonce') }}</textarea>
            </div>
            <div class="w-[60%] mb-4">
                <label class="block text-sm font-medium">Image du schéma</label>
                <input 
                    type="file" 
                    id="input-image" 
                    name="image" 
                    accept="image/*" 
                    class="border border-black/20 rounded w-full p-2">
            </div>
            <div class="w-40 mb-4">
                <label class="block text-sm font-medium">Points (total)</label>
                <input 
                    type="number" 
                    name="points" 
                    value="{{ old('points', 1) }}" 
                    min="0.1" step="0.1" 
                    class="border border-black/20 rounded w-full p-2">
            </div>
            <p class=" text-black/60 w-[60%] mb-2">
                Cliquez sur l'image ci-dessous pour placer une zone. Une zone = un endroit où l'étudiant devra déposer un mot.
            </p>
            <div class="flex gap-5">
                <div class="mb-4 flex-1 rounded-md bg-black/5 min-h-30">
                    <div id="image-container" class="relative overflow-hidden rounded-md border border-black/20 hidden">
                        <img id="preview-image" src="" class="max-w-full block">
                        <div id="zones-overlay"></div>
                    </div>
                </div>
    
                <div class="mb-4 w-[40%] p-2 rounded-md border border-black/5 bg-black/3">
                    <h4 class="font-semibold mb-2">Zones ajoutées (<span id="nombre-zones">0</span>)</h4>
                    <div id="liste-zones" class="space-y-2"></div>
                </div>
            </div>

            <div id="hidden-zones-container"></div>

            <button type="submit" id="submit-btn" class="bg-rouge text-white px-4 py-2 mt-4 rounded">
                Enregistrer
            </button>
        </form>
    </div>
</div>

{{-- ✅ Modal pour ajouter/modifier une zone --}}
<div id="zone-modal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-md p-5 w-full max-w-md">
        <h3 class="text-lg font-semibold mb-4">Nouvelle zone</h3>

        <div class="mb-3">
            <label class="block text-sm font-medium mb-1">Nom de la zone (optionnel)</label>
            <input type="text" id="modal-nom-zone" class="border rounded w-full p-2" placeholder="Ex: Zone 1">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Réponse correcte pour cette zone</label>
            <input type="text" id="modal-texte-zone" class="border rounded w-full p-2" placeholder="Ex: Cœur">
            <p id="modal-error" class="text-rouge text-sm mt-1 hidden">Veuillez renseigner la réponse.</p>
        </div>

        <div class="flex justify-end gap-2">
            <button type="button" id="modal-annuler" class="border px-4 py-2 rounded">
                Annuler
            </button>
            <button type="button" id="modal-valider" class="bg-vert text-white px-4 py-2 rounded">
                Ajouter la zone
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let zones = []; // { x, y, nom_zone, texte, numero }
    let prochainNumero = 1;
    let positionEnAttente = null; // { x, y } en attente de validation dans le modal

    const inputImage = document.getElementById('input-image');
    const imageContainer = document.getElementById('image-container');
    const previewImage = document.getElementById('preview-image');
    const zonesOverlay = document.getElementById('zones-overlay');
    const listeZonesEl = document.getElementById('liste-zones');
    const nombreZonesEl = document.getElementById('nombre-zones');

    const zoneModal = document.getElementById('zone-modal');
    const modalNomZone = document.getElementById('modal-nom-zone');
    const modalTexteZone = document.getElementById('modal-texte-zone');
    const modalError = document.getElementById('modal-error');
    const modalValider = document.getElementById('modal-valider');
    const modalAnnuler = document.getElementById('modal-annuler');

    // === Aperçu de l'image sélectionnée ===
    inputImage.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            previewImage.src = e.target.result;
            imageContainer.classList.remove('hidden');
            zones = [];
            afficherZones();
            afficherListeZones();
        };
        reader.readAsDataURL(file);
    });

    // === Clique sur l'image : ouvrir le modal pour saisir la zone ===
    previewImage.addEventListener('click', function (e) {
        const rect = previewImage.getBoundingClientRect();
        const x = ((e.clientX - rect.left) / rect.width) * 100;
        const y = ((e.clientY - rect.top) / rect.height) * 100;

        positionEnAttente = { x, y };
        ouvrirModal();
    });

    function ouvrirModal() {
        modalNomZone.value = 'Zone ' + prochainNumero;
        modalTexteZone.value = '';
        modalError.classList.add('hidden');
        zoneModal.classList.remove('hidden');
        zoneModal.classList.add('flex');
        modalTexteZone.focus();
    }

    function fermerModal() {
        zoneModal.classList.add('hidden');
        zoneModal.classList.remove('flex');
        positionEnAttente = null;
    }

    modalAnnuler.addEventListener('click', fermerModal);

    zoneModal.addEventListener('click', function (e) {
        if (e.target === zoneModal) fermerModal();
    });

    modalValider.addEventListener('click', function () {
        const texte = modalTexteZone.value.trim();

        if (!texte) {
            modalError.classList.remove('hidden');
            modalTexteZone.focus();
            return;
        }

        const nomZone = modalNomZone.value.trim() || ('Zone ' + prochainNumero);

        zones.push({
            x: positionEnAttente.x,
            y: positionEnAttente.y,
            nom_zone: nomZone,
            texte: texte,
            numero: prochainNumero,
        });

        prochainNumero++;

        afficherZones();
        afficherListeZones();
        fermerModal();
    });

    // Valider avec la touche Entrée dans le champ texte
    modalTexteZone.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            modalValider.click();
        }
    });

    function afficherZones() {
        zonesOverlay.innerHTML = '';

        zones.forEach(function (zone) {
            const marker = document.createElement('div');
            marker.className = 'absolute w-6 h-6 -ml-3 -mt-3 rounded-full bg-rouge text-white text-xs flex items-center justify-center font-bold border-2 border-white shadow';
            marker.style.left = zone.x + '%';
            marker.style.top = zone.y + '%';
            marker.innerText = zone.numero;
            marker.title = zone.texte;
            zonesOverlay.appendChild(marker);
        });
    }

    function afficherListeZones() {
        listeZonesEl.innerHTML = '';
        nombreZonesEl.innerText = zones.length;

        zones.forEach(function (zone, index) {
            const div = document.createElement('div');
            div.className = 'flex justify-between items-center border border-black/5 rounded p-2 text-sm';
            div.innerHTML = `
                <span><strong>${zone.numero}.</strong> ${zone.nom_zone} — réponse : <strong>${zone.texte}</strong> (x=${zone.x.toFixed(1)}%, y=${zone.y.toFixed(1)}%)</span>
                <button type="button" class="text-rouge btn-supprimer-zone" data-index="${index}">
                    <i class="fa-solid fa-trash"></i>
                </button>
            `;
            listeZonesEl.appendChild(div);
        });

        document.querySelectorAll('.btn-supprimer-zone').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const index = parseInt(this.dataset.index, 10);
                zones.splice(index, 1);
                afficherZones();
                afficherListeZones();
            });
        });
    }

    document.getElementById('gd-form').addEventListener('submit', function (e) {
        // if (zones.length === 0) {
        //     e.preventDefault();
        //     alert('Ajoutez au moins une zone sur l\'image.');
        //     return;
        // }

        const container = document.getElementById('hidden-zones-container');
        container.innerHTML = '';

        zones.forEach(function (zone, index) {
            const champs = {
                nom_zone: zone.nom_zone,
                position_x: zone.x.toFixed(2),
                position_y: zone.y.toFixed(2),
                texte: zone.texte,
            };

            for (const champ in champs) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `zones[${index}][${champ}]`;
                input.value = champs[champ];
                container.appendChild(input);
            }
        });

        const btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.innerText = 'Enregistrement...';
    });
});
</script>
@endsection