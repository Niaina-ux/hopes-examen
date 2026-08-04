<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\Examen;
use App\Models\Student;
use App\Models\StudentExamen;
use Illuminate\Http\Request;

class AdminExamenStudentController extends Controller
{
    public function show(Request $request, string $slug, Examen $examen)
{
    $tousLesStudentExamen = StudentExamen::where('examen_id', $examen->id)
        ->whereNotNull('date_examen')
        ->get();

    $datesDisponibles = $tousLesStudentExamen
        ->map(fn($se) => $se->date_examen->format('Y-m-d'))
        ->unique()
        ->sort()
        ->values();

    $nombreParDate = $tousLesStudentExamen
        ->groupBy(fn($se) => $se->date_examen->format('Y-m-d'))
        ->map(fn($group) => $group->count());

    // ✅ Daty farany foana no default (mitovy amin'izay natao teo aloha)
    $dateSelectionnee = $request->query('date', $datesDisponibles->last());

    $studentwithexam = StudentExamen::where('examen_id', $examen->id)
        ->when($dateSelectionnee, function ($query) use ($dateSelectionnee) {
            $query->whereDate('date_examen', $dateSelectionnee);
        })
        ->with('user.student')
        ->get();

    $userIds = $studentwithexam->pluck('user_id');
    $students = Student::whereIn('user_id', $userIds)->pluck('id', 'user_id');

    $attempts = ExamAttempt::where('examen_id', $examen->id)
        ->where('numero_tentative', 1)
        ->whereIn('student_id', $students->values())
        ->get()
        ->keyBy('student_id');

    return view('admin.examen.examen-student.show', compact(
        'slug', 'examen', 'studentwithexam', 'datesDisponibles',
        'dateSelectionnee', 'students', 'attempts', 'nombreParDate'
    ));
}

    // 
    public function create(string $slug, Examen $examen)
    {
        // Alaina ny user_id an'ny student rehetra EFA invité amin'ity examen ity
        $userIdsDejaInvites = StudentExamen::where('examen_id', $examen->id)
            ->pluck('user_id')
            ->toArray();

        // Ny student mitovy categorie amin'ny examen, saingy MANALA ireo efa invité
        $students = Student::where('categorie_id', $examen->categorie_id)
            ->whereNotIn('user_id', $userIdsDejaInvites)
            ->with('user')
            ->get();

        return view('admin.examen.examen-student.create', compact(
            'slug', 'examen', 'students'
        ));
    }

    public function store(Request $request, string $slug, Examen $examen)
    {
        $validated = $request->validate([
            'student_user_ids'   => ['required', 'array', 'min:1'],
            'student_user_ids.*' => ['exists:users,id'],
            'date_examen'        => ['required', 'date'], // mijanona mitovy, 'date' dia mahazaka Y-m-d ihany koa
        ], [
            'student_user_ids.required' => 'Veuillez sélectionner au moins un étudiant.',
            'date_examen.required'      => 'La date de l\'examen est obligatoire.',
        ]);

        // ✅ Mpianatra marina ihany (categorie_id mitovy amin'ny examen), miaro tsy azo ampidirina
        // mpianatra hafa categorie na dia manandrana manova ny HTML aza ny mpampiasa
        $studentsValides = Student::where('categorie_id', $examen->categorie_id)
            ->whereIn('user_id', $validated['student_user_ids'])
            ->pluck('user_id');

        if ($studentsValides->isEmpty()) {
            return back()->withErrors([
                'student_user_ids' => 'Aucun étudiant valide sélectionné pour cette catégorie.',
            ])->withInput();
        }

        foreach ($studentsValides as $userId) {
            StudentExamen::updateOrCreate(
                [
                    'examen_id' => $examen->id,
                    'user_id'   => $userId,
                ],
                [
                    'date_examen' => $validated['date_examen'],
                    'termine'     => false,
                ]
            );
        }

        return redirect()
            ->route('admin.examen.student.create', [$slug, $examen->id])
            ->with('success', 'Étudiants assignés à l\'examen avec succès.');
    }

    /**
     * Manaisotra mpianatra iray tsy hanao ity examen ity intsony
     */
    public function destroy(string $slug, Examen $examen, StudentExamen $studentExamen)
    {
        $studentExamen->delete();

        return redirect()
            ->back()
            ->with('success', 'Étudiant retiré de l\'examen.');
    }
}
