<x-app-layout>
    <div class="p-6 bg-gradient-to-r from-green-900 via-green-700 to-green-600 text-white font-extrabold text-center text-3xl rounded-t-2xl shadow-xl flex items-center justify-center gap-4">
        <span class="inline-block text-4xl">💚</span>
        <span>Willkommen im Feed</span>
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

        <form method="GET" action="{{ route('posts.index') }}" class="mb-4 flex items-center gap-2">
            <label class="text-sm font-semibold">Sortierung:</label>

            <select name="sort"
                    class="border border-black bg-gray-100 rounded px-3 py-2"
                    onchange="this.form.submit()">
                <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Neueste zuerst</option>
                <option value="most_likes" {{ $sort === 'most_likes' ? 'selected' : '' }}>Meiste Likes zuerst</option>
            </select>

            <span class="text-xs px-2 py-1 border border-black rounded bg-gray-100">
                Aktuell: {{ $sort === 'most_likes' ? 'Meiste Likes' : 'Neueste' }}
            </span>
        </form>

        {{-- 1) Post erstellen --}}
        <div class="bg-pink-50 shadow-lg rounded-lg p-6 border border-pink-200">
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
                        class="px-4 py-2 rounded border border-black bg-gray-200 text-black hover:bg-gray-300 transition">
                        Posten
                    </button>
                </div>
            </form>
        </div>

        {{-- 2) Feed --}}
        <div class="space-y-4">
            @foreach ($posts as $post)
                <div class="bg-white shadow rounded p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-semibold">
                                <a href="{{ route('profile.show', $post->user) }}"
                                    class="underline hover:text-blue-600">
                                    {{ $post->user->username ?? $post->user->name ?? 'Unbekannt' }} </a>
                            </div>

                            <div class="text-sm text-gray-500">
                                {{ $post->created_at->format('d.m.Y H:i') }}
                            </div>
                        </div>

                        <div class="text-sm text-gray-600">
                            Likes: <span class="font-semibold">{{ $post->likes_count }}</span>

                        </div>
                    </div>

                    <div class="mt-3 whitespace-pre-wrap">
                        {{ $post->content }}
                    </div>

                    <div class="mt-4 flex items-center gap-3">
                        @php
                            $liked = isset($likedPostIds[$post->id]);
                        @endphp

                        <form method="POST" action="{{ route('posts.like', $post) }}">
                            @csrf
                            <button
                                type="submit"
                                class="px-3 py-1 rounded border {{ $liked ? 'bg-gray-900 text-black' : 'bg-pink-500 text-black' }}"
                                @if($post->user_id === auth()->id()) disabled @endif
                            >
                                {{ $liked ? 'Unlike' : 'Like' }}
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
                                >Löschen</button>
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
