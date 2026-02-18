<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PostLike;


class PostController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->query('sort', 'newest');

    $query = Post::query()
        ->with('user')
        ->withCount('likes'); // -> likes_count

    if ($sort === 'most_likes') {
        $query->orderByDesc('likes_count')
              ->orderByDesc('created_at');
    } else {
        $query->orderByDesc('created_at');
    }

    $posts = $query->paginate(10)->withQueryString();

    // likedPostIds (damit dein Button Like/Unlike richtig weiß)
    $likedPostIds = [];
    if (Auth::check()) {
        $likedPostIds = PostLike::where('user_id', Auth::id())
            ->pluck('post_id')
            ->flip()       // macht [post_id => index] -> gut für isset()
            ->toArray();
    }

    return view('posts.index', compact('posts', 'sort', 'likedPostIds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:420'],
        ]);

        $request->user()->posts()->create($validated);

        return redirect()->route('posts.index')->with('success', 'Post erstellt!');
    }

    public function toggleLike(Post $post)
    {
        $user = Auth::user();

    // optional: eigene Posts nicht liken
    if ($post->user_id === $user->id) {
        return back();
    }

    $alreadyLiked = PostLike::where('post_id', $post->id)
        ->where('user_id', $user->id)
        ->exists();

    if ($alreadyLiked) {
        PostLike::where('post_id', $post->id)
            ->where('user_id', $user->id)
            ->delete();
    } else {
        PostLike::create([
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }

    return back();
    }

    public function destroy(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        $post->delete();

        return redirect()->route('dashboard')->with('success', 'Post gelöscht!');
    }

    public function edit(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:420'],
        ]);

        $post->update($validated);

        return redirect()->route('dashboard')->with('success', 'Post aktualisiert!');
    }
}
