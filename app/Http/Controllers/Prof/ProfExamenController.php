<?php

namespace App\Http\Controllers\Prof;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Code;
use App\Models\Examen;
use App\Models\Fichier;
use App\Models\GlisserDeposer;
use App\Models\ImageExercice;
use App\Models\MotsCroises;
use App\Models\Pointiller;
use App\Models\Qcm;
use App\Models\Redaction;
use App\Models\Relier;
use App\Models\Text;
use App\Models\TypeExercice;
use Illuminate\Http\Request;

class ProfExamenController extends Controller
{

    public function show(string $slug)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();

        $examens = Examen::where('categorie_id', $categorie->id)
        ->orderBy('id', 'desc')
        ->paginate(10);

        return view('prof.examen.show', compact('categorie', 'examens', 'slug'));
    }


    public function assignTypes(string $slug, Examen $examen)
    {
        $typesExercice = TypeExercice::all();

        return view('prof.examen.assign-types', compact('slug', 'examen', 'typesExercice'));
    }

    public function storeTypes(Request $request, string $slug,  Examen $examen)
    {
        $validated = $request->validate([
            'type_exercice_id'   => ['required', 'array', 'min:1'],
            'type_exercice_id.*' => ['exists:types_exercice,id'],
            'ordre'              => ['required', 'array'],
            'ordre.*'            => ['required', 'integer', 'min:0'],
        ], [
            'type_exercice_id.required' => 'Veuillez sélectionner au moins un type d\'exercice.',
            'ordre.required'            => 'Veuillez indiquer l\'ordre pour chaque type d\'exercice sélectionné.',
        ]);

        
        $ordresSelectionnes = [];
        foreach ($validated['type_exercice_id'] as $typeId) {
            if (!isset($validated['ordre'][$typeId]) || $validated['ordre'][$typeId] === '' || $validated['ordre'][$typeId] === null) {
                return back()->withErrors([
                    'ordre' => "Veuillez indiquer l'ordre pour chaque type d'exercice sélectionné.",
                ])->withInput();
            }

            $ordresSelectionnes[] = (int) $validated['ordre'][$typeId];
        }

        
        if (count($ordresSelectionnes) !== count(array_unique($ordresSelectionnes))) {
            return back()->withErrors([
                'ordre' => 'Deux types d\'exercice ne peuvent pas avoir le même ordre. Veuillez attribuer un ordre unique à chacun.',
            ])->withInput();
        }

        $syncData = [];
        foreach ($validated['type_exercice_id'] as $typeId) {
            $syncData[$typeId] = ['ordre' => $validated['ordre'][$typeId]];
        }

        $examen->typesExercice()->sync($syncData);

        return redirect()
            ->route('prof.examen.showtypes',[$slug, $examen->id] )
            ->with('success', 'Types d\'exercice ajoutés avec succès.');
    }
    

    public function showTypes(string $slug, Examen $examen)
    {
        $categorie = Categorie::where('slug', $slug)->firstOrFail();
        
        $examen->load('typesExercice');

        return view('prof.examen.examen-type', compact('examen', 'slug'));
    }


    public function terminerCreation(string $slug, Examen $examen)
    {
        $examen->load('typesExercice');
        $erreurs = [];

        foreach ($examen->typesExercice as $type) {
            switch ($type->slug) {
                case 'qcm':
                    $qcms = Qcm::where('examen_id', $examen->id)->with('qcmQuestions')->get();

                    
                    if ($qcms->isEmpty()) {
                        $erreurs[] = "Aucun exercice « QCM » n'a été créé pour ce type d'exercice.";
                        break;
                    }

                    foreach ($qcms as $qcm) {
                        $totalPoints = $qcm->qcmQuestions->sum('points');
                        if ($qcm->qcmQuestions->isEmpty()) {
                            $erreurs[] = "QCM « {$qcm->titre} » : aucune question n'a été ajoutée.";
                        } elseif ($totalPoints != $qcm->note_totale) {
                            $erreurs[] = "QCM « {$qcm->titre} » : {$totalPoints} pts au lieu de {$qcm->note_totale} pts attendus.";
                        }
                    }
                    break;

                case 'code':
                    $codes = Code::where('examen_id', $examen->id)->with('codeQuestions')->get();

                    if ($codes->isEmpty()) {
                        $erreurs[] = "Aucun exercice « Code » n'a été créé pour ce type d'exercice.";
                        break;
                    }

                    foreach ($codes as $code) {
                        $totalPoints = $code->codeQuestions->sum('points');
                        if ($code->codeQuestions->isEmpty()) {
                            $erreurs[] = "Code « {$code->titre} » : aucune question n'a été ajoutée.";
                        } elseif ($totalPoints != $code->note_totale) {
                            $erreurs[] = "Code « {$code->titre} » : {$totalPoints} pts au lieu de {$code->note_totale} pts attendus.";
                        }
                    }
                    break;

                case 'text':
                    $texts = Text::where('examen_id', $examen->id)->with('textQuestions')->get();

                    if ($texts->isEmpty()) {
                        $erreurs[] = "Aucun exercice « Compréhension de texte » n'a été créé pour ce type d'exercice.";
                        break;
                    }

                    foreach ($texts as $text) {
                        $totalPoints = $text->textQuestions->sum('points');
                        if ($text->textQuestions->isEmpty()) {
                            $erreurs[] = "Texte « {$text->titre} » : aucune question n'a été ajoutée.";
                        } elseif ($totalPoints != $text->note_totale) {
                            $erreurs[] = "Texte « {$text->titre} » : {$totalPoints} pts au lieu de {$text->note_totale} pts attendus.";
                        }
                    }
                    break;

                case 'redaction':
                    $redactions = Redaction::where('examen_id', $examen->id)->get();

                    if ($redactions->isEmpty()) {
                        $erreurs[] = "Aucun exercice « Rédaction » n'a été créé pour ce type d'exercice.";
                        break;
                    }
                    break;

                case 'pointiller':
                    $pointillers = Pointiller::where('examen_id', $examen->id)->with('pointillerQuestions')->get();

                    if ($pointillers->isEmpty()) {
                        $erreurs[] = "Aucun exercice « Pointillé » n'a été créé pour ce type d'exercice.";
                        break;
                    }

                    foreach ($pointillers as $pointiller) {
                        $totalPoints = $pointiller->pointillerQuestions->sum('points');
                        if ($pointiller->pointillerQuestions->isEmpty()) {
                            $erreurs[] = "Pointillé « {$pointiller->titre} » : aucune question n'a été ajoutée.";
                        } elseif ($totalPoints != $pointiller->note_totale) {
                            $erreurs[] = "Pointillé « {$pointiller->titre} » : {$totalPoints} pts au lieu de {$pointiller->note_totale} pts attendus.";
                        }
                    }
                    break;

                case 'relier':
                    $reliers = Relier::where('examen_id', $examen->id)->with('relierQuestions')->get();

                    if ($reliers->isEmpty()) {
                        $erreurs[] = "Aucun exercice « Relier » n'a été créé pour ce type d'exercice.";
                        break;
                    }

                    foreach ($reliers as $relier) {
                        $totalPoints = $relier->relierQuestions->sum('points');
                        if ($relier->relierQuestions->isEmpty()) {
                            $erreurs[] = "Relier « {$relier->titre} » : aucune question n'a été ajoutée.";
                        } elseif ($totalPoints != $relier->note_totale) {
                            $erreurs[] = "Relier « {$relier->titre} » : {$totalPoints} pts au lieu de {$relier->note_totale} pts attendus.";
                        }
                    }
                    break;

                case 'fichier':
                    $fichiers = Fichier::where('examen_id', $examen->id)->with('fichierQuestions')->get();

                    if ($fichiers->isEmpty()) {
                        $erreurs[] = "Aucun exercice « Devoir à rendre » n'a été créé pour ce type d'exercice.";
                        break;
                    }

                    foreach ($fichiers as $fichier) {
                        $totalPoints = $fichier->fichierQuestions->sum('points');
                        if ($fichier->fichierQuestions->isEmpty()) {
                            $erreurs[] = "Devoir « {$fichier->titre} » : aucune question n'a été ajoutée.";
                        } elseif ($totalPoints != $fichier->note_totale) {
                            $erreurs[] = "Devoir « {$fichier->titre} » : {$totalPoints} pts au lieu de {$fichier->note_totale} pts attendus.";
                        }
                    }
                    break;

                case 'image':
                    $images = ImageExercice::where('examen_id', $examen->id)->with('questions')->get();

                    if ($images->isEmpty()) {
                        $erreurs[] = "Aucun exercice « Image » n'a été créé pour ce type d'exercice.";
                        break;
                    }

                    foreach ($images as $image) {
                        $totalPoints = $image->questions->sum('points');
                        if ($image->questions->isEmpty()) {
                            $erreurs[] = "Image « {$image->titre} » : aucune image n'a été ajoutée.";
                        } elseif ($totalPoints != $image->note_totale) {
                            $erreurs[] = "Image « {$image->titre} » : {$totalPoints} pts au lieu de {$image->note_totale} pts attendus.";
                        }
                    }
                    break;

                case 'glisserdeposer':
                    $glisserDeposers = GlisserDeposer::where('examen_id', $examen->id)->with('questions')->get();

                    if ($glisserDeposers->isEmpty()) {
                        $erreurs[] = "Aucun exercice « Glisser-déposer » n'a été créé pour ce type d'exercice.";
                        break;
                    }

                    foreach ($glisserDeposers as $gd) {
                        $totalPoints = $gd->questions->sum('points');
                        if ($gd->questions->isEmpty()) {
                            $erreurs[] = "Glisser-déposer « {$gd->titre} » : aucune question n'a été ajoutée.";
                        } elseif ($totalPoints != $gd->note_totale) {
                            $erreurs[] = "Glisser-déposer « {$gd->titre} » : {$totalPoints} pts au lieu de {$gd->note_totale} pts attendus.";
                        }
                    }
                    break;

                case 'motscroises':
                    $motsCroisesListe = MotsCroises::where('examen_id', $examen->id)->with('motsCroisesMots')->get();

                    if ($motsCroisesListe->isEmpty()) {
                        $erreurs[] = "Aucun exercice « Mots croisés » n'a été créé pour ce type d'exercice.";
                        break;
                    }

                    foreach ($motsCroisesListe as $mc) {
                        $totalPoints = $mc->motsCroisesMots->sum('points');
                        if ($mc->motsCroisesMots->isEmpty()) {
                            $erreurs[] = "Mots croisés « {$mc->titre} » : aucun mot n'a été ajouté.";
                        } elseif ($totalPoints != $mc->note_totale) {
                            $erreurs[] = "Mots croisés « {$mc->titre} » : {$totalPoints} pts au lieu de {$mc->note_totale} pts attendus.";
                        }
                    }
                    break;
            }
        }

        if (!empty($erreurs)) {
            return back()->with('error', "Impossible de terminer : " . implode(' | ', $erreurs));
        }

        $examen->update(['status' => 'archive']);

        return redirect()
            ->route('prof.examen.show', $slug)
            ->with('success', 'Examen finalisé avec succès.');
    }
    
}
