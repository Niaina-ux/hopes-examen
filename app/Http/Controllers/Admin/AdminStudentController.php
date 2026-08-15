<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\ExamAttempt;
use App\Models\Student;
use App\Models\StudentExamen;
use App\Models\User;
use App\Traits\CalculeStatistiquesExamen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminStudentController extends Controller
{

    use CalculeStatistiquesExamen;
    
    public function index(Request $request)
    {

        $categories = Categorie::all();
        $students = User::where('role', 'student')
        ->with('student.categorie')
        ->when($request->filled('categorie_id'), function ($query) use ($request) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('categorie_id', $request->categorie_id);
            });
        })
        ->latest()
        ->paginate(10);

        return view('admin.student.index', compact('students','categories'));
    }

    public function create()
    {
        $categories = Categorie::all();

        $studentACompleter = null;
        if (session('student_id')) {
            $studentACompleter = User::find(session('student_id'));
        }

        return view('admin.student.create', compact('categories', 'studentACompleter'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('images/users'), $imageName);
            $imagePath = $imageName;
        }

        $student = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'image' => $imagePath,
            'password' => Hash::make($validated['password']),
            'password_affiche' => Crypt::encrypt($validated['password']),
            'role' => 'student',
        ]);

        return redirect()
            ->route('admin.student.create')
            ->with('success', 'Étudiant créé avec succès.')
            ->with('student_id', $student->id);
    }

    //ajouter categorie
    public function assignCategorie(User $student)
    {    
        $categories = Categorie::all();
        return view('admin.student.assign-categorie', compact('student', 'categories'));
    }


    public function storeCategorie(Request $request, User $student)
    {
        $validated = $request->validate([
            'matricule' => ['required', 'string', 'max:255', 'unique:students,matricule'],
            'categorie_id' => ['required', 'exists:categories,id'],
        ], [
            'matricule.required' => 'Le matricule est obligatoire.',
            'categorie_id.required' => 'Veuillez sélectionner une catégorie.',
        ]);

        Student::create([
            'user_id' => $student->id,
            'matricule' => $validated['matricule'],
            'categorie_id' => $validated['categorie_id'],
        ]);

        session()->forget('student_id');

        return redirect()
            ->route('admin.student.create')
            ->with('success', 'Étudiant assigné à sa catégorie avec succès.');
    }

    //supprimer
    public function destroy(User $student)
    {
        // effacer l'image dans images/..
        if ($student->image && file_exists(public_path('images/users/' . $student->image))) {
            unlink(public_path('images/users/' . $student->image));
        }

        $student->delete();

        return redirect()
            ->route('admin.student.index')
            ->with('success', 'Etudiant effacé avec succes.');
    }


    //detail d'un etudiant
    public function examenallstudent(int $studentId)
    {
        $student = User::find($studentId);

        if (!$student) {
            return redirect()
                ->route('admin.student.index');
        }

        $student->load('student.categorie');
        $userStudent = $student->student;

        if (!$userStudent) {
            return redirect()
                ->route('admin.student.index')
                ->with('error', 'Profil étudiant introuvable.');
        }

        $slug = $userStudent->categorie?->slug;

        $attempts = ExamAttempt::where('student_id', $userStudent->id)
            ->where('numero_tentative', 1)
            ->where('status', '!=', 'en_cours')
            ->with('examen.categorie')
            ->latest('date_fin')
            ->get();

        $statistiques = $attempts
            ->filter(fn($attempt) => $attempt->status === 'corrige')
            ->map(function ($attempt) {
                return [
                    'titre' => $attempt->examen->titre,
                    'date' => $attempt->date_fin?->format('d/m/Y'),
                    'pourcentage' => $this->calculerPourcentageAttempt($attempt->examen, $attempt),
                ];
            })
            ->filter(fn($s) => $s['pourcentage'] !== null)
            ->sortBy('date')
            ->values();

        $moyenneGenerale = $statistiques->isNotEmpty()
            ? round($statistiques->avg('pourcentage'), 1)
            : null;

        $examen_planifie = StudentExamen::with('examen.categorie')
            ->where('user_id', $student->id)
            ->where('termine', false)
            ->whereHas('examen', function ($query) {
                $query->where('status', '!=', 'brouillon');
            })
            ->orderBy('id', 'asc')
            ->get();

        return view('admin.student.examenallstudent', compact(
            'student', 'userStudent', 'slug', 'attempts', 'examen_planifie', 'statistiques', 'moyenneGenerale'
        ));
    }
    
}
