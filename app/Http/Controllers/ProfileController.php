<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show(Request $request, User $user)
    {
        // Posts des Users (neueste zuerst) + Like-Anzahl pro Post
        $posts = $user->posts()
            ->withCount('likes')
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
        'benutzername' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,benutzername,' . $user->id],
        'profilbeschreibung' => ['nullable', 'string', 'max:255'],
    ]);

    $user->update($data);

    return redirect()
        ->route('profile.show', $user)
        ->with('success', 'Profil gespeichert!');
        
    }
}
