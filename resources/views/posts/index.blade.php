<x-app-layout>
    <div class="p-6 bg-gradient-to-r from-pink-200 via-pink-300 to-fuchsia-200 text-pink-700 font-extrabold text-center text-3xl rounded-t-2xl shadow-2xl flex items-center justify-center gap-4 border-4 border-pink-200">
        <span class="inline-block text-4xl">💖</span>
        <span class="drop-shadow-lg tracking-wide">Willkommen im Feed</span>
    </div>

    <x-slot name="header">
        <h2 class="font-semibold text-xl">Feed</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto p-4 space-y-6">

        @if (session('success'))
            <div class="p-3 rounded bg-green-100 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        {{-- SORTIERUNG --}}
        @php
            $sort = $sort ?? request('sort', 'newest');
        @endphp

        <form method="GET" action="{{ route('posts.index') }}" class="mb-4 flex items-center gap-2 bg-gradient-to-r from-pink-50 via-pink-100 to-fuchsia-50 border border-pink-200 shadow px-4 py-3 rounded-lg">
            <label class="text-sm font-semibold text-pink-700">Sortierung:</label>

            <select name="sort"
                    class="border border-pink-300 bg-white px-3 py-2 text-pink-700 font-semibold shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-200 transition"
                    style="border-radius: 0.5rem;" onchange="this.form.submit()">
                <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Neueste zuerst</option>
                <option value="most_likes" {{ $sort === 'most_likes' ? 'selected' : '' }}>Meiste Likes zuerst</option>
            </select>

            <span class="text-xs px-2 py-1 border border-pink-200 bg-white text-pink-700 font-semibold shadow-sm" style="border-radius: 0.5rem;">
                Aktuell: {{ $sort === 'most_likes' ? 'Meiste Likes' : 'Neueste' }}
            </span>
        </form>

        {{-- 1) Post erstellen --}}
        <div class="bg-gradient-to-br from-pink-50 via-pink-100 to-fuchsia-50 shadow-xl rounded-2xl p-6 border border-pink-100">
            <form method="POST" action="{{ route('posts.store') }}" class="space-y-3">
                @csrf

                <textarea
                    name="content"
                    maxlength="420"
                    rows="3"
                    class="w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Was denkst du gerade? (max 420 Zeichen)"
                >{{ old('content') }}</textarea>

                @error('content')
                    <div class="text-red-600 text-sm">{{ $message }}</div>
                @enderror

                <div class="flex justify-end">
                    <button type="submit"
                        class="px-4 py-2 rounded-full border-2 border-pink-200 bg-gradient-to-r from-pink-100 via-pink-200 to-fuchsia-100 text-pink-700 font-bold shadow-md transition-all duration-200 hover:scale-105 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-pink-200">
                        <span class="font-semibold">Posten</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- 2) Feed --}}
        <div class="space-y-4">
            @foreach ($posts as $post)
                <div class="bg-gradient-to-br from-pink-50 via-pink-100 to-fuchsia-50 shadow-xl rounded-2xl p-6 border border-pink-100 hover:shadow-2xl transition-all duration-200">
                    {{-- Header --}}
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-semibold">
                                <a href="{{ route('profile.show', ['user' => $post->user->benutzername]) }}"
                                   class="underline hover:text-blue-600">
                                    {{ $post->user->benutzername ?? $post->user->name ?? 'Unbekannt' }}
                                </a>
                            </div>

                            <div class="text-sm text-gray-500">
                                {{ $post->created_at->format('d.m.Y H:i') }}
                            </div>
                        </div>

                        <div class="text-sm text-gray-600">
                            <span class="font-semibold">❤️ {{ $post->likes_count }}</span>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="mt-3 whitespace-pre-line text-left leading-relaxed text-lg">
                        {{ $post->content }}
                    </div>

                    {{-- Kommentare --}}
                    <div class="mt-4 border-t pt-3 space-y-2">
                        <div class="text-sm font-semibold">
                            Kommentare ({{ $post->comments->count() }})
                        </div>

                        {{-- Liste --}}
                        @foreach($post->comments as $comment)
                            <div class="text-sm bg-gray-50 border border-black rounded p-2">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <span class="font-semibold">
                                            <a class="underline"
                                               href="{{ route('profile.show', ['user' => $comment->user->benutzername]) }}">
                                                {{ $comment->user->benutzername }}
                                            </a>
                                        </span>
                                        <span class="text-gray-500 text-xs">
                                            • {{ $comment->created_at->format('d.m.Y H:i') }}
                                        </span>
                                    </div>

                                    @if($comment->user_id === auth()->id())
                                        <form method="POST" action="{{ route('comments.destroy', $comment) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-xs px-2 py-1 border border-red-600 text-red-600 rounded"
                                                    onclick="return confirm('Kommentar löschen?')">
                                                Löschen
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <div class="mt-1 whitespace-pre-wrap">{{ $comment->content }}</div>
                            </div>
                        @endforeach

                        {{-- Formular --}}
                        <form method="POST" action="{{ route('comments.store', $post) }}" class="flex gap-2 mt-2">
                            @csrf
                            <input
                                type="text"
                                name="content"
                                maxlength="500"
                                class="flex-1 border border-black rounded px-3 py-2"
                                placeholder="Kommentar schreiben..."
                            />
                            <button type="submit"
                                    class="px-4 py-2 rounded-full border-2 border-pink-200 bg-gradient-to-r from-pink-100 via-pink-200 to-fuchsia-100 text-pink-700 font-bold shadow-md transition-all duration-200 hover:scale-105 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-pink-200">
                                <span class="font-semibold">Senden</span>
                            </button>
                        </form>

                        @error('content')
                            <div class="text-red-600 text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Buttons --}}
                    <div class="mt-4 flex items-center gap-3">
                        @php
                            $liked = isset($likedPostIds[$post->id]);
                        @endphp

                        <form method="POST" action="{{ route('posts.like', $post) }}">
                            @csrf
                            <button
                                type="submit"
                                class="px-4 py-2 rounded-full border-2 border-pink-200 shadow-lg transition-all duration-200 font-bold flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-pink-200 disabled:opacity-60 disabled:cursor-not-allowed
                                    {{ $liked ? 'bg-gradient-to-r from-pink-200 via-pink-300 to-fuchsia-200 text-pink-700 scale-105' : 'bg-white text-pink-600 hover:bg-pink-100 hover:scale-105' }}"
                                @if($post->user_id === auth()->id()) disabled @endif
                            >
                                <span class="text-xl">{{ $liked ? '💖' : '🤍' }}</span>
                                <span>{{ $liked ? 'Unlike' : 'Like' }}</span>
                            </button>
                        </form>

                        @if($post->user_id === auth()->id())
                            <form method="POST" action="{{ route('posts.destroy', $post) }}">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="px-3 py-1 rounded border border-red-600 text-red-600 bg-gray-100 hover:bg-red-600 hover:text-white transition"
                                    onclick="return confirm('Post wirklich löschen?')"
                                >
                                    Löschen
                                </button>
                            </form>

                            <a href="{{ route('posts.edit', $post) }}"
                               class="px-3 py-1 rounded border border-black bg-gray-100 text-black hover:bg-gray-200 transition">
                                Bearbeiten
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach

            <div>
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
