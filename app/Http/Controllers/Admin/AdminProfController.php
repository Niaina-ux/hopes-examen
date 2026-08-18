<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Prof;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminProfController extends Controller
{
   public function index(Request $request)
{
    $categories = Categorie::orderBy('nom')->get();

    $query = User::where('role', 'prof')
        ->with('prof.categorie');

    if ($request->filled('categorie_id')) {
        $query->whereHas('prof', function ($q) use ($request) {
            $q->where('categorie_id', $request->categorie_id);
        });
    }

    $profs = $query->latest()->get();

    return view('admin.prof.index', compact(
        'profs',
        'categories'
    ));
}

    public function create()
    {
        $categories = Categorie::all();
        $profACompleter = null;

        return view('admin.prof.create', compact('categories', 'profACompleter'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::min(6)],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images'), $imageName);
        }

        $prof = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'password_affiche' => Crypt::encrypt($validated['password']),
            'role' => 'prof',
            'image' => $imageName,
        ]);

        $categories = Categorie::all();
        $profACompleter = $prof;

        return view('admin.prof.create', compact('categories', 'profACompleter'))
            ->with('success', 'Professeur créé avec succès. Complétez la catégorie ci-dessous.');
    }
  
    //ajout categorie
    public function assignCategorie(User $prof)
    {
        
        $categories = Categorie::all();
        return view('admin.prof.assign-categorie', compact('prof', 'categories'));
    }

    public function storeCategorie(Request $request, User $prof)
    {
        $validated = $request->validate([
            'categorie_id' => ['required', 'exists:categories,id'],
        ]);

        Prof::updateOrCreate(
            ['user_id' => $prof->id],
            ['categorie_id' => $validated['categorie_id']]
        );

        return redirect()
            ->route('admin.prof.index')
            ->with('success',  $prof->name . 'est ajouté dans un damine d\'examen!' );
    }

    public function destroy(User $prof)
    {
        // effacer l'image dans images/..
        if ($prof->image && file_exists(public_path('images/' . $prof->image))) {
            unlink(public_path('images/' . $prof->image));
        }

        $prof->delete();

        return redirect()
            ->route('admin.prof.index')
            ->with('success', 'Professeur effacé avec succes.');
    }
}
