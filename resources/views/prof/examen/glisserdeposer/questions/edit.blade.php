@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="w-full py-3">
    <a href="{{ route('prof.examen.glisserdeposer.question.index', [$slug, $examen->id, $glisserdeposer->id]) }}">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div class="bg-white p-4 rounded-md">
        <h2 class="text-xl font-semibold mb-4">Modifier la question — {{ $glisserdeposer->titre }}</h2>

        @if($errors->any())
            <div class="mb-4 p-3 rounded-md bg-red-50 border border-rouge text-rouge text-sm">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form id="gd-form" action="{{ route('prof.examen.glisserdeposer.question.update', [$slug, $examen->id, $glisserdeposer->id, $question->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-base font-medium">Énoncé (optionnel)</label>
                <textarea name="enonce" rows="2" class="border rounded w-full p-2" placeholder="Ex: Placez chaque élément à sa place sur le schéma">{{ old('enonce', $question->enonce) }}</textarea>
            </div>

            <div class="flex gap-4 mb-4">
                <div class="flex-1">
                    <label class="block text-base font-medium">Image du schéma</label>
                    @if($question->image)
                        <p class="text-xs text-black/50 mb-1">Laissez vide pour conserver l'image actuelle.</p>
                    @endif
                    <input type="file" id="input-image" name="image" accept="image/*" class="border rounded w-full p-2">
                </div>
                <div class="w-40">
                    <label class="block text-base font-medium">Points (total)</label>
                    <input type="number" name="points" value="{{ old('points', $question->points) }}" min="0.1" step="0.1" class="border rounded w-full p-2">
                </div>
            </div>

            <div class="mb-4">
                <p class="text-sm text-black/60 mb-2">
                    Cliquez sur l'image ci-dessous pour placer une zone. Une zone = un endroit où l'étudiant devra déposer un mot.
                </p>
                <div id="image-container" class="relative inline-block border border-black/20 {{ $question->image ? '' : 'hidden' }}">
                    <img id="preview-image" src="{{ $question->image ? asset('images/glisserdeposer/' . $question->image) : '' }}" class="max-w-full block">
                    <div id="zones-overlay"></div>
                </div>
            </div>

            <div class="mb-4">
                <h4 class="font-semibold mb-2">Zones ajoutées (<span id="nombre-zones">0</span>)</h4>
                <div id="liste-zones" class="space-y-2"></div>
            </div>

            <div id="hidden-zones-container"></div>

            <button type="submit" id="submit-btn" class="bg-rouge text-white px-4 py-2 rounded">
                Enregistrer les modifications
            </button>
        </form>
    </div>
</div>

{{-- Modal pour ajouter/modifier une zone --}}
<div id="zone-modal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-md p-5 w-full max-w-md">
        <h3 class="text-lg font-semibold mb-4" id="modal-titre">Nouvelle zone</h3>

        <div class="mb-3">
            <label class="block text-sm font-medium mb-1">Nom de la zone (optionnel)</label>
            <input type="text" id="modal-nom-zone" class="border rounded w-full p-2" placeholder="Ex: Zone 1">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Réponse correcte pour cette zone</label>
            <input type="text" id="modal-texte-zone" class="border rounded w-full p-2" placeholder="Ex: Cœur">
            <p id="modal-error" class="text-rouge text-sm mt-1 hidden">Veuillez renseigner la réponse.</p>
        </div>

        <div class="flex justify-between gap-2">
            <button type="button" id="modal-supprimer" class="text-rouge px-4 py-2 rounded hidden">
                <i class="fa-solid fa-trash"></i> Supprimer
            </button>
            <div class="flex gap-2 ms-auto">
                <button type="button" id="modal-annuler" class="border px-4 py-2 rounded">
                    Annuler
                </button>
                <button type="button" id="modal-valider" class="bg-vert text-white px-4 py-2 rounded">
                    Ajouter la zone
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let zones = []; // { x, y, nom_zone, texte, numero }
    let prochainNumero = 1;
    let positionEnAttente = null;
    let indexEnEdition = null; // ✅ si on modifie une zone existante (clic sur un marker)

    const inputImage = document.getElementById('input-image');
    const imageContainer = document.getElementById('image-container');
    const previewImage = document.getElementById('preview-image');
    const zonesOverlay = document.getElementById('zones-overlay');
    const listeZonesEl = document.getElementById('liste-zones');
    const nombreZonesEl = document.getElementById('nombre-zones');

    const zoneModal = document.getElementById('zone-modal');
    const modalTitre = document.getElementById('modal-titre');
    const modalNomZone = document.getElementById('modal-nom-zone');
    const modalTexteZone = document.getElementById('modal-texte-zone');
    const modalError = document.getElementById('modal-error');
    const modalValider = document.getElementById('modal-valider');
    const modalAnnuler = document.getElementById('modal-annuler');
    const modalSupprimer = document.getElementById('modal-supprimer');

    // ✅ Zones déjà enregistrées (venant du serveur)
    const zonesExistantes = @json($zonesExistantes);

    function chargerZonesExistantes() {
        zones = [];
        prochainNumero = 1;

        zonesExistantes.forEach(function (zoneData) {
            zones.push({
                x: zoneData.x,
                y: zoneData.y,
                nom_zone: zoneData.nom_zone,
                texte: zoneData.texte,
                numero: prochainNumero,
            });
            prochainNumero++;
        });

        afficherZones();
        afficherListeZones();
    }

    // === Aperçu de l'image sélectionnée (si une nouvelle image est choisie) ===
    inputImage.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            previewImage.src = e.target.result;
            imageContainer.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    });

    // === Clique sur l'image : ouvrir le modal pour une NOUVELLE zone ===
    previewImage.addEventListener('click', function (e) {
        const rect = previewImage.getBoundingClientRect();
        const x = ((e.clientX - rect.left) / rect.width) * 100;
        const y = ((e.clientY - rect.top) / rect.height) * 100;

        positionEnAttente = { x, y };
        indexEnEdition = null;
        ouvrirModal();
    });

    function ouvrirModal() {
        const estEdition = indexEnEdition !== null;

        modalTitre.innerText = estEdition ? 'Modifier la zone' : 'Nouvelle zone';
        modalValider.innerText = estEdition ? 'Enregistrer' : 'Ajouter la zone';
        modalSupprimer.classList.toggle('hidden', !estEdition);

        if (estEdition) {
            const zone = zones[indexEnEdition];
            modalNomZone.value = zone.nom_zone;
            modalTexteZone.value = zone.texte;
        } else {
            modalNomZone.value = 'Zone ' + prochainNumero;
            modalTexteZone.value = '';
        }

        modalError.classList.add('hidden');
        zoneModal.classList.remove('hidden');
        zoneModal.classList.add('flex');
        modalTexteZone.focus();
    }

    function fermerModal() {
        zoneModal.classList.add('hidden');
        zoneModal.classList.remove('flex');
        positionEnAttente = null;
        indexEnEdition = null;
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

        if (indexEnEdition !== null) {
            // ✅ Modification d'une zone existante
            zones[indexEnEdition].nom_zone = nomZone;
            zones[indexEnEdition].texte = texte;
        } else {
            // Nouvelle zone
            zones.push({
                x: positionEnAttente.x,
                y: positionEnAttente.y,
                nom_zone: nomZone,
                texte: texte,
                numero: prochainNumero,
            });
            prochainNumero++;
        }

        afficherZones();
        afficherListeZones();
        fermerModal();
    });

    modalSupprimer.addEventListener('click', function () {
        if (indexEnEdition !== null) {
            zones.splice(indexEnEdition, 1);
            afficherZones();
            afficherListeZones();
            fermerModal();
        }
    });

    modalTexteZone.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            modalValider.click();
        }
    });

    function afficherZones() {
        zonesOverlay.innerHTML = '';

        zones.forEach(function (zone, index) {
            const marker = document.createElement('div');
            marker.className = 'absolute w-6 h-6 -ml-3 -mt-3 rounded-full bg-rouge text-white text-xs flex items-center justify-center font-bold border-2 border-white shadow cursor-pointer';
            marker.style.left = zone.x + '%';
            marker.style.top = zone.y + '%';
            marker.innerText = zone.numero;
            marker.title = zone.texte;

            // ✅ Cliquer sur un marker existant permet de le modifier/supprimer
            marker.addEventListener('click', function (e) {
                e.stopPropagation();
                indexEnEdition = index;
                ouvrirModal();
            });

            zonesOverlay.appendChild(marker);
        });
    }

    function afficherListeZones() {
        listeZonesEl.innerHTML = '';
        nombreZonesEl.innerText = zones.length;

        zones.forEach(function (zone, index) {
            const div = document.createElement('div');
            div.className = 'flex justify-between items-center border rounded p-2 text-sm';
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
        if (zones.length === 0) {
            e.preventDefault();
            alert('Ajoutez au moins une zone sur l\'image.');
            return;
        }

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

    // ✅ Charge les zones existantes au démarrage
    chargerZonesExistantes();
});
</script>
@endsection