<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show(Request $request, User $user)
    {
        // Posts des Users (neueste zuerst) + Like-Anzahl pro Post
        $posts = $user->posts()
            ->with('user')
            ->withCount('likes')
            ->with(['comments.user'])   // ✅ Kommentare + User laden
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Anzahl erstellter Posts
        $postCount = $user->posts()->count();

        // Gesamtanzahl Likes auf allen Posts
        $totalLikes = $user->posts()
            ->withCount('likes')
            ->get()
            ->sum('likes_count');

        return view('profile.show', [
            'profileUser' => $user,
            'posts'       => $posts,
            'postCount'   => $postCount,
            'totalLikes'  => $totalLikes,
        ]);

        
    }

    public function edit()
    {
        $user = Auth::user();

        return view('profile.edit', [
            'user' => $user,
        ]);
    }
     public function update(Request $request)
    {
        $user = Auth::user();

    $data = $request->validate([
        'name'  => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],

        // ✅ NUR prüfen, wenn es im Formular wirklich mitgeschickt wird
        'benutzername' => ['sometimes', 'required', 'string', 'max:50', 'alpha_dash', 'unique:users,benutzername,' . $user->id],

        // ✅ Profilbeschreibung speichern
        'profilbeschreibung' => ['nullable', 'string', 'max:255'],
    ]);

    $user->update($data);

    return redirect()
        ->route('profile.show', ['user' => $user->benutzername])
        ->with('success', 'Profil gespeichert!');
        
    }
    /**
     * Entfernt das aktuell eingeloggte Benutzerkonto.
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();

        // Optional: Passwort-Bestätigung prüfen
        $request->validate([
            'password' => ['required'],
        ]);
        if (!\Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Das Passwort ist falsch.'])->with('userDeletion', true);
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Dein Account wurde gelöscht.');
    }
}
