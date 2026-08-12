@extends('layouts.prof-layouts.proflayoutshead')
@section('contenue-prof')
<div class="w-full py-3">
    <div class="">
        <a href="{{ route('prof.examen.motscroises', [$slug, $examen->id]) }}">
            Retour /
        </a>
        <span class="font-semibold">Création</span>
    </div>
    <div class="bg-white rounded-md me-2">
        <h2 class="text-2xl font-semibold mb-4 pb-1 text-vert border-b-2 border-black/10">Créer un exercice mots croisés</h2>
        @if($errors->any())
            <div class="mb-4 p-3 rounded-md bg-red-50 border border-rouge text-rouge text-sm">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        <form id="mc-form" action="{{ route('prof.examen.motscroises.store', [$slug, $examen->id]) }}" method="POST"
            class="bg-black/1 border border-black/3 rounded-md p-4">
            @csrf
            <div class="mb-4">
                <label class="block text-base font-medium">Titre</label>
                <input type="text" name="titre" value="{{ old('titre') }}" class="formulaire border border-black/10 bg-white rounded w-full p-2" placeholder="Ex: Mots croisés - Vocabulaire">
            </div>

            <div class="mb-4">
                <label class="block text-base font-medium">Description (optionnel)</label>
                <textarea name="description" rows="2" class="formulaire border border-black/10 bg-white rounded w-full p-2">{{ old('description') }}</textarea>
            </div>

            <div class="flex gap-4 mb-4 w-[5cm]">
                <div class="flex-1 hidden">
                    <label class="block text-base font-medium">Durée (minutes)</label>
                    <input type="text" name="duree_minutes" value="{{ old('duree_minutes') }}" min="1" class="formulaire border bg-white border-black/10 rounded w-full p-2">
                </div>
                <div class="flex-1">
                    <label class="block text-base font-medium">Note totale</label>
                    <input type="text" name="note_totale" value="{{ old('note_totale', 10) }}" min="0.1" step="0.1" class="formulaire border bg-white border-black/10 rounded w-full p-2">
                </div>
            </div>

            <hr class="my-4 border-black/10">
            {{-- Étape 1 : Dimension de la grille --}}
            <div class="mb-4">
                <label class="block text-base font-medium mb-2">Dimension de la grille</label>
                <div class="flex gap-3 items-end">
                    <div>
                        <label class="block text-xs text-black/50">Largeur (colonnes)</label>
                        <input type="text" id="grille-largeur" value="10" min="2" max="20" class="formulaire border bg-white border-black/10 rounded p-2 w-24">
                    </div>
                    <div>
                        <label class="block text-xs text-black/50">Hauteur (lignes)</label>
                        <input type="text" id="grille-hauteur" value="10" min="2" max="20" class="formulaire border bg-white border-black/10 rounded p-2 w-24">
                    </div>
                    <button type="button" id="btn-generer-grille" class="bg-black/5 border rounded-md px-4 py-2">
                        Générer la grille
                    </button>
                </div>
            </div>

            {{-- Étape 2 : Grille interactive --}}
            <div class="">
                <label class="block text-base font-medium mb-2">Grille (cliquez sur une case pour démarrer un mot)</label>
                <p class="text-xs text-black/50 mb-2">
                    <span class="inline-block w-3 h-3 bg-black/5 border border-black/10 align-middle"></span> case vide
                    &nbsp;&nbsp;
                    <span class="inline-block w-3 h-3 bg-white border border-black/20 align-middle"></span> lettre masquée pour l'étudiant
                    &nbsp;&nbsp;
                    <span class="inline-block w-3 h-3 bg-black/20 border border-black/20 align-middle"></span> lettre indice (visible pour l'étudiant)
                </p>
                <div id="grille-container" class="inline-block border rounded-md border-black/20"></div>
            </div>

            {{-- Panneau d'ajout de mot (masqué par défaut) --}}
            <div id="panneau-ajout-mot" class="hidden border border-black/20 rounded-md p-4 mb-4 bg-vert/5">
                <h4 class="font-semibold mb-3">Nouveau mot — case de départ : <span id="case-depart-label"></span></h4>

                <div class="flex gap-4 mb-3">
                    <div class="flex-1">
                        <label class="block text-xs text-black/50">Direction</label>
                        <select id="input-direction" class="formulaire border border-black/10 rounded bg-white w-full p-2">
                            <option value="horizontal">Horizontal</option>
                            <option value="vertical">Vertical</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs text-black/50">Réponse (mot)</label>
                        <input type="text" id="input-reponse" class="formulaire border border-black/10 bg-white rounded w-full p-2 uppercase" maxlength="20" placeholder="Ex: PARIS">
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs text-black/50">Points</label>
                        <input type="text" id="input-points" value="1" min="0.1" step="0.1" class="formulaire border bg-white border-black/10 rounded w-full p-2">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="block text-xs text-black/50">Indice</label>
                    <input type="text" id="input-indice" class="formulaire border border-black/10 bg-white rounded w-full p-2" placeholder="Ex: Capitale de la France">
                </div>

                <div class="mb-3">
                    <label class="block text-xs text-black/50 mb-1">Lettres à révéler (optionnel)</label>
                    <div id="lettres-visibles-container" class="flex gap-2 flex-wrap"></div>
                </div>

                <div class="flex gap-2">
                    <button type="button" id="btn-valider-mot" class="bg-vert text-white px-4 py-2 rounded">
                        Ajouter ce mot à la grille
                    </button>
                    <button type="button" id="btn-annuler-mot" class="border px-4 py-2 rounded">
                        Annuler
                    </button>
                </div>
            </div>

            {{-- Liste des mots ajoutés --}}
            <div class="mb-4">
                <h4 class="font-semibold mb-2">Mots ajoutés (<span id="nombre-mots">0</span>)</h4>
                <div id="liste-mots" class="space-y-2"></div>
            </div>

            <input type="hidden" name="largeur" id="hidden-largeur">
            <input type="hidden" name="hauteur" id="hidden-hauteur">
            <div id="hidden-mots-container"></div>

            <button type="submit" id="submit-btn" class="bg-rouge text-white px-4 py-2 rounded">
                Enregistrer l'exercice
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let largeur = 10;
    let hauteur = 10;
    let grille = []; // grille[y][x] = { lettre: null, motIds: [] }
    let mots = []; // { id, indice, reponse, direction, position_x, position_y, numero, points, positions_lettres_visibles }
    let prochainNumero = 1;
    let caseDepart = null; // { x, y }

    const grilleContainer = document.getElementById('grille-container');
    const panneauAjout = document.getElementById('panneau-ajout-mot');
    const caseDepartLabel = document.getElementById('case-depart-label');
    const inputReponse = document.getElementById('input-reponse');
    const lettresVisiblesContainer = document.getElementById('lettres-visibles-container');
    const listeMotsEl = document.getElementById('liste-mots');
    const nombreMotsEl = document.getElementById('nombre-mots');

    // === Générer la grille vide ===
    function genererGrille() {
        largeur = parseInt(document.getElementById('grille-largeur').value, 10);
        hauteur = parseInt(document.getElementById('grille-hauteur').value, 10);

        grille = [];
        for (let y = 0; y < hauteur; y++) {
            grille[y] = [];
            for (let x = 0; x < largeur; x++) {
                grille[y][x] = { lettre: null, motIds: [] };
            }
        }

        mots = [];
        prochainNumero = 1;
        afficherGrille();
        afficherListeMots();
    }

    // === Manamarina raha "case" (x,y) dia toerana "hint" (litera aseho amin'ny mpianatra) ===
    function estLettreVisible(x, y) {
        for (const mot of mots) {
            const longueur = mot.reponse.length;

            for (let i = 0; i < longueur; i++) {
                const mx = mot.direction === 'horizontal' ? mot.position_x + i : mot.position_x;
                const my = mot.direction === 'horizontal' ? mot.position_y : mot.position_y + i;

                if (mx === x && my === y && mot.positions_lettres_visibles.includes(i)) {
                    return true;
                }
            }
        }
        return false;
    }

    // === Afficher la grille en HTML ===
    function afficherGrille() {
        grilleContainer.innerHTML = '';

        for (let y = 0; y < hauteur; y++) {
            const ligne = document.createElement('div');
            ligne.className = 'flex';

            for (let x = 0; x < largeur; x++) {
                const cellule = grille[y][x];
                const div = document.createElement('div');

                let bgClass = 'bg-black/5 hover:bg-vert/10'; // case vide (aucun mot)

                if (cellule.lettre) {
                    // ✅ Case avec lettre "hint" (visible pour l'étudiant) = bg-black/20
                    // Case avec lettre masquée (l'étudiant devra la trouver) = bg-white
                    bgClass = estLettreVisible(x, y) ? 'bg-black/20 font-bold' : 'bg-white font-bold';
                }

                div.className = 'relative w-9 h-9 border border-black/10 flex items-center justify-center cursor-pointer select-none ' + bgClass;
                div.dataset.x = x;
                div.dataset.y = y;

                const numeroCase = trouverNumeroCase(x, y);
                if (numeroCase) {
                    const spanNum = document.createElement('span');
                    spanNum.className = 'absolute top-0 left-0.5 text-[8px] text-black/50';
                    spanNum.innerText = numeroCase;
                    div.appendChild(spanNum);
                }

                if (cellule.lettre) {
                    const spanLettre = document.createElement('span');
                    spanLettre.innerText = cellule.lettre;
                    div.appendChild(spanLettre);
                }

                div.addEventListener('click', function () {
                    onClickCase(x, y);
                });

                ligne.appendChild(div);
            }

            grilleContainer.appendChild(ligne);
        }
    }

    function trouverNumeroCase(x, y) {
        for (const mot of mots) {
            if (mot.position_x === x && mot.position_y === y) {
                return mot.numero;
            }
        }
        return null;
    }

    // === Clique sur une case : démarrer un nouveau mot ===
    function onClickCase(x, y) {
        caseDepart = { x, y };
        caseDepartLabel.innerText = `(x=${x}, y=${y})`;
        inputReponse.value = '';
        document.getElementById('input-indice').value = '';
        document.getElementById('input-points').value = 1;
        lettresVisiblesContainer.innerHTML = '';
        panneauAjout.classList.remove('hidden');
        panneauAjout.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // === Générer les checkboxes de lettres visibles selon le mot tapé ===
    inputReponse.addEventListener('input', function () {
        this.value = this.value.toUpperCase().replace(/[^A-Z]/g, '');
        genererCheckboxLettres();
    });

    function genererCheckboxLettres() {
        const reponse = inputReponse.value;
        lettresVisiblesContainer.innerHTML = '';

        for (let i = 0; i < reponse.length; i++) {
            const label = document.createElement('label');
            label.className = 'flex flex-col items-center gap-1 border rounded p-1 cursor-pointer text-xs';
            label.innerHTML = `
                <span class="font-mono font-bold">${reponse[i]}</span>
                <input type="checkbox" class="lettre-visible-checkbox" value="${i}">
            `;
            lettresVisiblesContainer.appendChild(label);
        }
    }

    // === Valider l'ajout du mot ===
    document.getElementById('btn-valider-mot').addEventListener('click', function () {
        const reponse = inputReponse.value.trim();
        const direction = document.getElementById('input-direction').value;
        const indice = document.getElementById('input-indice').value.trim();
        const points = parseFloat(document.getElementById('input-points').value) || 1;

        if (!reponse || reponse.length < 2) {
            alert('Le mot doit contenir au moins 2 lettres.');
            return;
        }
        if (!indice) {
            alert('Veuillez renseigner un indice.');
            return;
        }

        const longueur = reponse.length;
        const finX = direction === 'horizontal' ? caseDepart.x + longueur - 1 : caseDepart.x;
        const finY = direction === 'horizontal' ? caseDepart.y : caseDepart.y + longueur - 1;

        if (finX >= largeur || finY >= hauteur) {
            alert('Le mot dépasse les limites de la grille.');
            return;
        }

        for (let i = 0; i < longueur; i++) {
            const x = direction === 'horizontal' ? caseDepart.x + i : caseDepart.x;
            const y = direction === 'horizontal' ? caseDepart.y : caseDepart.y + i;
            const celluleExistante = grille[y][x];

            if (celluleExistante.lettre && celluleExistante.lettre !== reponse[i]) {
                alert(`Conflit à la position (x=${x}, y=${y}) : la lettre "${reponse[i]}" ne correspond pas à la lettre existante "${celluleExistante.lettre}".`);
                return;
            }
        }

        const positionsVisibles = Array.from(document.querySelectorAll('.lettre-visible-checkbox:checked'))
            .map(cb => parseInt(cb.value, 10));

        const mot = {
            id: mots.length + 1,
            indice: indice,
            reponse: reponse,
            direction: direction,
            position_x: caseDepart.x,
            position_y: caseDepart.y,
            numero: prochainNumero++,
            points: points,
            positions_lettres_visibles: positionsVisibles,
        };

        for (let i = 0; i < longueur; i++) {
            const x = direction === 'horizontal' ? caseDepart.x + i : caseDepart.x;
            const y = direction === 'horizontal' ? caseDepart.y : caseDepart.y + i;
            grille[y][x].lettre = reponse[i];
            grille[y][x].motIds.push(mot.id);
        }

        mots.push(mot);

        panneauAjout.classList.add('hidden');
        afficherGrille();
        afficherListeMots();
    });

    document.getElementById('btn-annuler-mot').addEventListener('click', function () {
        panneauAjout.classList.add('hidden');
    });

    // === Afficher la liste des mots ajoutés, avec bouton supprimer ===
    function afficherListeMots() {
        listeMotsEl.innerHTML = '';
        nombreMotsEl.innerText = mots.length;

        mots.forEach(function (mot, index) {
            const div = document.createElement('div');
            div.className = 'flex justify-between items-center border rounded p-2 text-sm';
            div.innerHTML = `
                <span><strong>${mot.numero}.</strong> ${mot.reponse} (${mot.direction}, x=${mot.position_x}, y=${mot.position_y}) — ${mot.indice} — ${mot.points} pt(s)</span>
                <button type="button" class="text-rouge btn-supprimer-mot" data-index="${index}">
                    <i class="fa-solid fa-trash"></i>
                </button>
            `;
            listeMotsEl.appendChild(div);
        });

        document.querySelectorAll('.btn-supprimer-mot').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const index = parseInt(this.dataset.index, 10);
                supprimerMot(index);
            });
        });
    }

    function supprimerMot(index) {
        const mot = mots[index];

        const longueur = mot.reponse.length;
        for (let i = 0; i < longueur; i++) {
            const x = mot.direction === 'horizontal' ? mot.position_x + i : mot.position_x;
            const y = mot.direction === 'horizontal' ? mot.position_y : mot.position_y + i;

            grille[y][x].motIds = grille[y][x].motIds.filter(id => id !== mot.id);

            if (grille[y][x].motIds.length === 0) {
                grille[y][x].lettre = null;
            }
        }

        mots.splice(index, 1);
        afficherGrille();
        afficherListeMots();
    }

    // === Soumission du formulaire : sérialiser les données ===
    document.getElementById('mc-form').addEventListener('submit', function (e) {
        if (mots.length === 0) {
            e.preventDefault();
            alert('Ajoutez au moins un mot dans la grille avant d\'enregistrer.');
            return;
        }

        document.getElementById('hidden-largeur').value = largeur;
        document.getElementById('hidden-hauteur').value = hauteur;

        const container = document.getElementById('hidden-mots-container');
        container.innerHTML = '';

        mots.forEach(function (mot, index) {
            const champs = {
                indice: mot.indice,
                reponse: mot.reponse,
                direction: mot.direction,
                position_x: mot.position_x,
                position_y: mot.position_y,
                numero: mot.numero,
                points: mot.points,
            };

            for (const champ in champs) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `mots[${index}][${champ}]`;
                input.value = champs[champ];
                container.appendChild(input);
            }

            mot.positions_lettres_visibles.forEach(function (pos) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `mots[${index}][positions_lettres_visibles][]`;
                input.value = pos;
                container.appendChild(input);
            });
        });

        const btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.innerText = 'Enregistrement...';
    });

    document.getElementById('btn-generer-grille').addEventListener('click', genererGrille);

    // Génère une grille par défaut au chargement
    genererGrille();
});
</script>
@endsection